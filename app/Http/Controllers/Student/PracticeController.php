<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PastPaper;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PracticeController extends Controller
{
    /**
     * Show available practice papers.
     */
    public function index(Request $request): View
    {
        $pastPapers = PastPaper::query()
            ->where('status', 'analyzed')
            ->with([
                'subject',
                'schoolClass',
            ])
            ->withCount([
                'questions' => function ($query) {
                    $query->where('status', 'approved');
                },
            ])
            ->latest('exam_year')
            ->latest('id')
            ->get();

        return view('student.practice.index', [
            'pastPapers' => $pastPapers,
        ]);
    }

    /**
     * Show a practice paper overview.
     */
    public function show(
        Request $request,
        PastPaper $pastPaper
    ): View {
        abort_unless(
            $pastPaper->status === 'analyzed',
            404
        );

        $pastPaper->load([
            'subject',
            'schoolClass',
            'questions',
            'questionSections' => function ($query) {
                $query->withCount([
                    'questions' => function ($query) {
                        $query->where('status', 'approved');
                    },
                ]);
            },
        ]);

        return view('student.practice.show', [
            'pastPaper' => $pastPaper,
        ]);
    }

    /**
     * Start a fresh practice attempt.
     */
    public function start(
        Request $request,
        PastPaper $pastPaper
    ): RedirectResponse {
        abort_unless(
            $pastPaper->status === 'analyzed',
            404
        );

        $questions = Question::query()
            ->where('past_paper_id', $pastPaper->id)
            ->where('status', 'approved')
            ->with([
                'options',
                'section',
            ])
            ->orderBy('section_id')
            ->orderBy('id')
            ->get();

        abort_if(
            $questions->isEmpty(),
            404,
            'No approved questions are available for this practice paper.'
        );

        /*
         * Store a randomized option order for every MCQ.
         *
         * This order belongs to this attempt only.
         * Starting the paper again will create a new order.
         */
        $optionOrders = [];

        foreach ($questions as $question) {
            if ($question->question_type === 'multiple_choice') {
                $optionOrders[(string) $question->id] = $question->options
                    ->shuffle()
                    ->pluck('id')
                    ->values()
                    ->all();
            }
        }

        $attempt = [
            'past_paper_id' => $pastPaper->id,
            'question_ids' => $questions
                ->pluck('id')
                ->values()
                ->all(),

            'option_orders' => $optionOrders,

            'answers' => [],

            'current_index' => 0,
        ];

        $request->session()->put(
            'student_practice_attempt',
            $attempt
        );

        return redirect()->route(
            'student.practice.attempt',
            $pastPaper
        );
    }

    /**
     * Display the current question.
     */
    public function attempt(
        Request $request,
        PastPaper $pastPaper
    ): View {
        $attempt = $request->session()->get(
            'student_practice_attempt'
        );

        abort_unless(
            is_array($attempt)
            && ($attempt['past_paper_id'] ?? null) === $pastPaper->id,
            404
        );

        $questionIds = $attempt['question_ids'] ?? [];

        abort_if(
            empty($questionIds),
            404,
            'This practice attempt is no longer available.'
        );

        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->where('past_paper_id', $pastPaper->id)
            ->where('status', 'approved')
            ->with([
                'options',
                'section',
            ])
            ->orderByRaw(
                'FIELD(id, ' .
                implode(',', array_map('intval', $questionIds)) .
                ')'
            )
            ->get();

        abort_if(
            $questions->isEmpty(),
            404,
            'No questions are available for this practice attempt.'
        );

        /*
         * Make sure the current index is still valid.
         */
        $currentIndex = (int) (
            $attempt['current_index'] ?? 0
        );

        if ($currentIndex < 0) {
            $currentIndex = 0;
        }

        if ($currentIndex >= $questions->count()) {
            $currentIndex = $questions->count() - 1;
        }

        /*
         * Keep corrected index in the session.
         */
        $attempt['current_index'] = $currentIndex;

        $request->session()->put(
            'student_practice_attempt',
            $attempt
        );

        $pastPaper->load([
            'subject',
            'schoolClass',
        ]);

        $currentQuestion = $questions->get($currentIndex);

        /*
         * Restore the randomized MCQ option order for this attempt.
         */
        $optionOrders = $attempt['option_orders'] ?? [];

        if (
            $currentQuestion->question_type === 'multiple_choice'
            && isset(
                $optionOrders[(string) $currentQuestion->id]
            )
        ) {
            $order = $optionOrders[
                (string) $currentQuestion->id
            ];

            $orderedOptions = $currentQuestion->options
                ->sortBy(function ($option) use ($order) {
                    $position = array_search(
                        $option->id,
                        $order,
                        true
                    );

                    return $position === false
                        ? PHP_INT_MAX
                        : $position;
                })
                ->values();

            $currentQuestion->setRelation(
                'options',
                $orderedOptions
            );
        }

        $answers = $attempt['answers'] ?? [];

        return view('student.practice.attempt', [
            'pastPaper' => $pastPaper,
            'questions' => $questions,
            'currentIndex' => $currentIndex,
            'currentQuestion' => $currentQuestion,
            'answers' => $answers,
        ]);
    }

    /**
     * Save the current answer and move through the exam.
     */
    public function answer(
        Request $request,
        PastPaper $pastPaper,
        Question $question
    ): RedirectResponse {
        $attempt = $request->session()->get(
            'student_practice_attempt'
        );

        abort_unless(
            is_array($attempt)
            && ($attempt['past_paper_id'] ?? null) === $pastPaper->id,
            404
        );

        $questionIds = $attempt['question_ids'] ?? [];

        abort_unless(
            in_array($question->id, $questionIds, true),
            404
        );

        abort_unless(
            $question->past_paper_id === $pastPaper->id
            && $question->status === 'approved',
            404
        );

        /*
         * Different question types need different input handling.
         */
        if ($question->question_type === 'multiple_choice') {
            $request->validate([
                'answer' => [
                    'nullable',
                    'string',
                    'max:20',
                ],
            ]);
        } else {
            $request->validate([
                'answer' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],
            ]);
        }

        $answer = $request->input('answer');

        /*
         * Save answer using question ID.
         */
        $answers = $attempt['answers'] ?? [];

        $answers[(string) $question->id] = $answer;

        $attempt['answers'] = $answers;

        /*
         * Determine current location.
         */
        $currentIndex = (int) (
            $attempt['current_index'] ?? 0
        );

        /*
         * Determine direction.
         */
        $direction = $request->input(
            'direction',
            'next'
        );

        if ($direction === 'previous') {
            $currentIndex--;
        } elseif ($direction === 'next') {
            $currentIndex++;
        }

        /*
         * Keep index inside valid range.
         */
        $currentIndex = max(
            0,
            min(
                $currentIndex,
                count($questionIds) - 1
            )
        );

        $attempt['current_index'] = $currentIndex;

        $request->session()->put(
            'student_practice_attempt',
            $attempt
        );

        return redirect()->route(
            'student.practice.attempt',
            $pastPaper
        );
    }

    /**
     * Submit the practice attempt and calculate the result.
     */
    public function submit(
        Request $request,
        PastPaper $pastPaper
    ): RedirectResponse {
        $attempt = $request->session()->get(
            'student_practice_attempt'
        );

        if (
            ! $attempt
            || ($attempt['past_paper_id'] ?? null) != $pastPaper->id
        ) {
            return redirect()
                ->route(
                    'student.practice.show',
                    $pastPaper
                )
                ->with(
                    'error',
                    'Your practice attempt has expired. Please start again.'
                );
        }

        /*
         * Save the answer currently displayed on screen.
         */
        $currentIndex = (int) (
            $attempt['current_index'] ?? 0
        );

        $questionIds = $attempt['question_ids'] ?? [];

        if (
            isset($questionIds[$currentIndex])
            && $request->filled('answer')
        ) {
            $currentQuestionId = (int) (
                $questionIds[$currentIndex]
            );

            $attempt['answers'][
                (string) $currentQuestionId
            ] = (string) $request->input('answer');
        }

        /*
         * Load the questions belonging to this attempt.
         */
        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->where('status', 'approved')
            ->with([
                'options',
                'section',
                'subject',
                'schoolClass',
            ])
            ->get()
            ->sortBy(function ($question) use ($questionIds) {
                $position = array_search(
                    $question->id,
                    $questionIds,
                    true
                );

                return $position === false
                    ? PHP_INT_MAX
                    : $position;
            })
            ->values();

        $answers = $attempt['answers'] ?? [];

        $automaticMarks = 0;
        $manualMarks = 0;
        $totalMarks = 0;

        $questionResults = [];

        foreach ($questions as $question) {
            $totalMarks += (int) $question->marks;

            $studentAnswer = $answers[
                (string) $question->id
            ] ?? null;

            /*
             * =====================================================
             * WRITING / ESSAY
             * =====================================================
             */
            if (
                in_array(
                    $question->question_type,
                    [
                        'essay',
                        'long_answer',
                        'writing',
                    ],
                    true
                )
            ) {
                $manualMarks += (int) $question->marks;

                $questionResults[] = [
                    'question_id' => $question->id,
                    'question' => $question->question,
                    'question_type' => $question->question_type,
                    'student_answer' => $studentAnswer,
                    'correct_answer' => $question->answer,
                    'marks_awarded' => 0,
                    'marks_available' => (int) $question->marks,
                    'status' => 'manual',
                    'marking_method' => 'manual',
                ];

                continue;
            }

            /*
             * =====================================================
             * MULTIPLE CHOICE
             * =====================================================
             *
             * The submitted answer is the QuestionOption ID.
             */
            if (
                $question->question_type === 'multiple_choice'
            ) {
                $correctOption = $question->options
                    ->firstWhere('is_correct', true);

                $correctOptionId = $correctOption?->id;

                $correct = (
                    $studentAnswer !== null
                    && $studentAnswer !== ''
                    && $correctOptionId !== null
                    && (int) $studentAnswer ===
                        (int) $correctOptionId
                );

                $marksAwarded = $correct
                    ? (int) $question->marks
                    : 0;

                $automaticMarks += $marksAwarded;

                /*
                 * Find the student's selected option so the
                 * results page can display its actual text.
                 */
                $selectedOption = null;

                if (
                    $studentAnswer !== null
                    && $studentAnswer !== ''
                ) {
                    $selectedOption = $question->options
                        ->firstWhere(
                            'id',
                            (int) $studentAnswer
                        );
                }

                $questionResults[] = [
                    'question_id' => $question->id,
                    'question' => $question->question,
                    'question_type' => $question->question_type,

                    'student_answer' =>
                        $selectedOption?->option_text
                        ?? $studentAnswer,

                    'correct_answer' =>
                        $correctOption?->option_text,

                    'marks_awarded' => $marksAwarded,

                    'marks_available' =>
                        (int) $question->marks,

                    'status' =>
                        $correct
                            ? 'correct'
                            : 'incorrect',

                    'marking_method' => 'automatic',
                ];

                continue;
            }

            /*
             * =====================================================
             * SHORT ANSWER
             * =====================================================
             */
            if (
                $question->question_type === 'short_answer'
            ) {
                $markingResult =
                    $this->markShortAnswerQuestion(
                        $question,
                        $studentAnswer
                    );

                $marksAwarded = (int) (
                    $markingResult['marks_awarded'] ?? 0
                );

                $automaticMarks += $marksAwarded;

                $status = $markingResult['correct']
                    ? 'correct'
                    : (
                        $marksAwarded > 0
                            ? 'partial'
                            : 'incorrect'
                    );

                $questionResults[] = [
                    'question_id' => $question->id,
                    'question' => $question->question,
                    'question_type' => $question->question_type,
                    'student_answer' => $studentAnswer,
                    'correct_answer' => $question->answer,
                    'marks_awarded' => $marksAwarded,
                    'marks_available' => (int) $question->marks,
                    'status' => $status,
                    'marking_method' =>
                        $markingResult['marking_method'],

                    'matched_points' =>
                        $markingResult['matched_points']
                        ?? null,

                    'total_marking_points' =>
                        $markingResult[
                            'total_marking_points'
                        ] ?? null,
                ];

                continue;
            }

            /*
             * =====================================================
             * FALLBACK
             * =====================================================
             */
            $correct = $this->answersMatch(
                $studentAnswer,
                $question->answer
            );

            $marksAwarded = $correct
                ? (int) $question->marks
                : 0;

            $automaticMarks += $marksAwarded;

            $questionResults[] = [
                'question_id' => $question->id,
                'question' => $question->question,
                'question_type' => $question->question_type,
                'student_answer' => $studentAnswer,
                'correct_answer' => $question->answer,
                'marks_awarded' => $marksAwarded,
                'marks_available' => (int) $question->marks,
                'status' => $correct
                    ? 'correct'
                    : 'incorrect',
                'marking_method' => 'exact_match',
            ];
        }

        /*
         * Store completed result.
         */
        $result = [
            'past_paper_id' => $pastPaper->id,

            'automatic_marks' =>
                $automaticMarks,

            'manual_marks' =>
                $manualMarks,

            'total_marks' =>
                $totalMarks,

            'question_results' =>
                $questionResults,

            'submitted_at' =>
                now()->toDateTimeString(),
        ];

        $request->session()->put(
            'student_practice_result',
            $result
        );

        /*
         * Practice attempt is finished.
         */
        $request->session()->forget(
            'student_practice_attempt'
        );

        return redirect()->route(
            'student.practice.results',
            $pastPaper
        );
    }

    /**
     * Display the submitted practice result.
     */
    public function results(
        Request $request,
        PastPaper $pastPaper
    ): View|RedirectResponse {
        $result = $request->session()->get(
            'student_practice_result'
        );

        if (
            ! $result
            || ($result['past_paper_id'] ?? null)
                != $pastPaper->id
        ) {
            return redirect()
                ->route(
                    'student.practice.show',
                    $pastPaper
                )
                ->with(
                    'error',
                    'No practice result was found.'
                );
        }

        $result['automatic_marks'] = (int) (
            $result['automatic_marks'] ?? 0
        );

        $result['manual_marks'] = (int) (
            $result['manual_marks'] ?? 0
        );

        $result['total_marks'] = (int) (
            $result['total_marks'] ?? 0
        );

        $result['question_results'] =
            $result['question_results'] ?? [];

        $pastPaper->load([
            'subject',
            'schoolClass',
        ]);

        return view(
            'student.practice.results',
            [
                'pastPaper' => $pastPaper,
                'result' => $result,
            ]
        );
    }

    /**
     * Compare a student's short answer with the stored answer.
     */
    private function answersMatch(
        ?string $studentAnswer,
        ?string $correctAnswer
    ): bool {
        if (
            $studentAnswer === null
            || trim($studentAnswer) === ''
            || $correctAnswer === null
            || trim($correctAnswer) === ''
        ) {
            return false;
        }

        $student = $this->normalizeAnswer(
            $studentAnswer
        );

        $correct = $this->normalizeAnswer(
            $correctAnswer
        );

        return $student === $correct;
    }

    /**
     * Normalize answer text.
     */
    private function normalizeAnswer(
        string $answer
    ): string {
        $answer = mb_strtolower(
            trim($answer)
        );

        $answer = str_replace(
            [
                '’',
                '‘',
                '“',
                '”',
                '"',
                '–',
                '—',
                ';',
            ],
            [
                "'",
                "'",
                '"',
                '"',
                '"',
                '-',
                '-',
                ',',
            ],
            $answer
        );

        $answer = preg_replace(
            '/[.,!?():]/u',
            ' ',
            $answer
        );

        $answer = preg_replace(
            '/\s+/u',
            ' ',
            $answer
        );

        return trim($answer);
    }

    /**
     * Return the number of marking points successfully identified
     * in the student's answer.
     */
    private function countMatchedMarkingPoints(
        string $studentAnswer,
        array $markingPoints
    ): int {
        if (
            trim($studentAnswer) === ''
            || empty($markingPoints)
        ) {
            return 0;
        }

        $student = $this->normalizeAnswer(
            $studentAnswer
        );

        $matched = 0;

        /*
         * Controlled synonym groups.
         *
         * These allow reasonable paraphrases without requiring
         * an external AI request for every student answer.
         */
        $synonyms = [
            'audience' => [
                'audience',
                'crowd',
                'spectators',
                'people',
            ],

            'close' => [
                'close',
                'connection',
                'connected',
                'near',
                'nearby',
            ],

            'friendly' => [
                'friendly',
                'lovely',
                'nice',
                'welcoming',
                'kind',
            ],

            'home' => [
                'home',
                'house',
                'residence',
            ],

            'quiet' => [
                'quiet',
                'silent',
                'peaceful',
                'calm',
            ],

            'walk' => [
                'walk',
                'walking',
                'on foot',
            ],

            'theatre' => [
                'theatre',
                'theater',
            ],

            'staff' => [
                'staff',
                'employees',
                'workers',
            ],
        ];

        foreach ($markingPoints as $point) {
            if (
                ! is_string($point)
                || trim($point) === ''
            ) {
                continue;
            }

            $point = $this->normalizeAnswer(
                $point
            );

            /*
             * Exact phrase match.
             */
            if (str_contains($student, $point)) {
                $matched++;
                continue;
            }

            /*
             * Extract meaningful words.
             */
            $words = preg_split(
                '/\s+/',
                $point
            );

            $stopWords = [
                'a',
                'an',
                'and',
                'are',
                'as',
                'at',
                'be',
                'by',
                'can',
                'for',
                'from',
                'he',
                'her',
                'his',
                'in',
                'is',
                'it',
                'of',
                'on',
                'or',
                'she',
                'the',
                'their',
                'there',
                'they',
                'to',
                'very',
                'was',
                'were',
                'with',
            ];

            $meaningfulWords = [];

            foreach ($words as $word) {
                $word = trim($word);

                if (
                    mb_strlen($word) < 4
                    || in_array(
                        $word,
                        $stopWords,
                        true
                    )
                ) {
                    continue;
                }

                $meaningfulWords[] = $word;
            }

            if (empty($meaningfulWords)) {
                continue;
            }

            /*
             * Count direct or synonym matches.
             */
            $wordMatches = 0;

            foreach ($meaningfulWords as $word) {

                /*
                 * Direct match.
                 */
                if (str_contains($student, $word)) {
                    $wordMatches++;
                    continue;
                }

                /*
                 * Synonym match.
                 */
                $matchedSynonym = false;

                foreach ($synonyms as $group) {

                    if (
                        ! in_array(
                            $word,
                            $group,
                            true
                        )
                    ) {
                        continue;
                    }

                    foreach ($group as $synonym) {

                        if (
                            str_contains(
                                $student,
                                $synonym
                            )
                        ) {
                            $matchedSynonym = true;
                            break;
                        }
                    }

                    if ($matchedSynonym) {
                        break;
                    }
                }

                if ($matchedSynonym) {
                    $wordMatches++;
                }
            }

            /*
             * Decide whether this marking point is satisfied.
             *
             * For one or two meaningful words, require all.
             *
             * For longer marking points, allow approximately
             * 60% of meaningful words to match.
             */
            $requiredMatches =
                count($meaningfulWords) <= 2
                    ? count($meaningfulWords)
                    : (int) ceil(
                        count($meaningfulWords) * 0.6
                    );

            if (
                $wordMatches >= $requiredMatches
            ) {
                $matched++;
            }
        }

        return $matched;
    }

    /**
     * Mark a short-answer question using marking points.
     */
    private function markShortAnswerQuestion(
        Question $question,
        ?string $studentAnswer
    ): array {
        if (
            $studentAnswer === null
            || trim($studentAnswer) === ''
        ) {
            return [
                'marks_awarded' => 0,
                'marks_available' => (int) $question->marks,
                'correct' => false,
                'manual' => false,
                'marking_method' => 'marking_points',
                'matched_points' => 0,
                'total_marking_points' => 0,
            ];
        }

        $metadata = $question->metadata ?? [];

        $markingPoints =
            $metadata['marking_points'] ?? [];

        /*
         * If no marking points exist, use the original
         * exact-answer fallback.
         */
        if (empty($markingPoints)) {

            $correct = $this->answersMatch(
                $studentAnswer,
                $question->answer
            );

            return [
                'marks_awarded' => $correct
                    ? (int) $question->marks
                    : 0,

                'marks_available' =>
                    (int) $question->marks,

                'correct' => $correct,

                'manual' => false,

                'marking_method' => 'exact_match',

                'matched_points' => null,

                'total_marking_points' => null,
            ];
        }

        $matchedPoints =
            $this->countMatchedMarkingPoints(
                $studentAnswer,
                $markingPoints
            );

        /*
         * Never award more marks than the question carries.
         *
         * Example:
         *
         * 4 marking points
         * 3 available marks
         *
         * Maximum = 3.
         */
        $marksAwarded = min(
            $matchedPoints,
            (int) $question->marks
        );

        return [
            'marks_awarded' => $marksAwarded,

            'marks_available' =>
                (int) $question->marks,

            'correct' =>
                $marksAwarded >=
                (int) $question->marks,

            'manual' => false,

            'marking_method' =>
                'marking_points',

            'matched_points' =>
                $matchedPoints,

            'total_marking_points' =>
                count($markingPoints),
        ];
    }
}

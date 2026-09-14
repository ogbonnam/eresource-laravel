<?php

namespace App\Services;

use App\Models\PastPaper;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class PastPaperQuestionAnalyzer
{
    /**
     * Analyze a processed past paper and create question records.
     */
    public function analyze(PastPaper $pastPaper): void
    {
        if ($pastPaper->status !== 'processed') {
            throw new RuntimeException(
                'The past paper must be processed before it can be analyzed.'
            );
        }

        if (blank($pastPaper->extracted_text)) {
            throw new RuntimeException(
                'The past paper has no extracted text to analyze.'
            );
        }

        try {
            $pastPaper->update([
                'status' => 'analyzing',
                'processing_error' => null,
            ]);

            $text = $this->prepareText(
                $pastPaper->extracted_text
            );

            $response = $this->sendToAi(
                $pastPaper,
                $text
            );

            $questions = $this->parseResponse($response);

            if (empty($questions)) {
                throw new RuntimeException(
                    'The AI returned no questions.'
                );
            }

            /*
             * Remove questions from a previous analysis of this paper.
             *
             * This makes re-analysis possible while we are developing.
             */
            $pastPaper->questions()->delete();

            foreach ($questions as $questionData) {
                $this->storeQuestion(
                    $pastPaper,
                    $questionData
                );
            }

            $pastPaper->update([
                'status' => 'analyzed',
                'processing_error' => null,
            ]);
        } catch (Throwable $e) {
            $error = $this->cleanUtf8(
                $e->getMessage()
            );

            Log::error(
                'Past paper question analysis failed',
                [
                    'past_paper_id' => $pastPaper->id,
                    'error' => $error,
                ]
            );

            $pastPaper->update([
                'status' => 'failed',
                'processing_error' => $error,
            ]);

            throw $e;
        }
    }

    /**
     * Clean extracted PDF/DOCX text before sending it to AI.
     */
    protected function prepareText(string $text): string
    {
        $text = $this->cleanUtf8($text);

        /*
         * Remove common PDF/download-site noise.
         */
        $patterns = [
            '/DO NOT WRITE IN THIS MARGIN/iu',
            '/Re-uploading, mirroring or re-hosting.*?(?:\n|$)/iu',
            '/Licensed for hosting on.*?(?:\n|$)/iu',
            '/Downloaded from.*?(?:\n|$)/iu',
            '/Trace ID:.*?(?:\n|$)/iu',
        ];

        foreach ($patterns as $pattern) {
            $text = preg_replace(
                $pattern,
                '',
                $text
            ) ?? $text;
        }

        /*
         * Remove obvious repeated blank lines.
         */
        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        ) ?? $text;

        /*
         * Do not aggressively remove whitespace.
         *
         * Line structure can help the AI understand:
         *
         * Question 1
         * ...
         * [1]
         *
         * Question 2
         * ...
         */
        return trim($text);
    }

    /**
     * Send the paper to Gemini through the OpenAI-compatible endpoint.
     */
    protected function sendToAi(
        PastPaper $pastPaper,
        string $text
    ): string {
        $endpoint = config('services.ai.endpoint');
        $apiKey = config('services.ai.key');
        $model = config('services.ai.model');

        if (blank($endpoint)) {
            throw new RuntimeException(
                'AI endpoint is not configured.'
            );
        }

        if (blank($apiKey)) {
            throw new RuntimeException(
                'AI API key is not configured.'
            );
        }

        if (blank($model)) {
            throw new RuntimeException(
                'AI model is not configured.'
            );
        }

        $prompt = $this->buildPrompt(
            $pastPaper,
            $text
        );

        $response = Http::timeout(180)
            ->withToken($apiKey)
            ->acceptJson()
            ->post($endpoint, [
                'model' => $model,

                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],

                /*
                 * Gemini structured output through its
                 * OpenAI-compatible Chat Completions API.
                 */
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'past_paper_questions',
                        'strict' => true,
                        'schema' => $this->questionSchema(),
                    ],
                ],

                /*
                 * Low temperature is intentional.
                 *
                 * We are extracting information, not generating
                 * creative content.
                 */
                'temperature' => 0.1,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'AI request failed: ' .
                $this->cleanUtf8(
                    $response->body()
                )
            );
        }

        $content = data_get(
            $response->json(),
            'choices.0.message.content'
        );

        if (
            ! is_string($content) ||
            blank($content)
        ) {
            throw new RuntimeException(
                'The AI response did not contain message content.'
            );
        }

        return $content;
    }

    /**
     * System instructions.
     */
    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
You are an examination-paper analysis engine.

Your job is to analyze the supplied examination paper and identify
every actual examinable question and sub-question.

DO NOT INVENT QUESTIONS.

Preserve the original wording of each question as closely as possible.

Ignore non-question material such as:

- page headers
- page numbers
- repeated instructions
- copyright notices
- download-site notices
- trace IDs
- "DO NOT WRITE IN THIS MARGIN"
- decorative characters
- corrupted PDF/OCR characters
- duplicated footer text

You must distinguish between:

1. Examination instructions
2. Reading passages or source material
3. Actual questions
4. Sub-questions
5. Answer options
6. Marks

For every actual question, identify:

- question number
- section or exercise
- question text
- question type
- marks
- topic
- subtopic
- concept
- difficulty
- command word
- answer, if determinable
- explanation, if determinable

For multiple-choice questions, extract every answer option.

Do not create options for non-multiple-choice questions.

For reading-comprehension questions, use the supplied reading passage
when determining the answer.

If the paper does not provide enough information to determine an answer,
leave the answer as null.

Do not confuse example text, instructions, or reading passages with
questions.

Return only the JSON structure required by the supplied schema.
PROMPT;
    }

    /**
     * Build the user prompt.
     */
    protected function buildPrompt(
        PastPaper $pastPaper,
        string $text
    ): string {
        $subject = $pastPaper->subject?->name ?? 'Unknown';
        $class = $pastPaper->schoolClass?->name ?? 'Unknown';

        return <<<PROMPT
Analyze the following examination paper.

PAPER INFORMATION

Subject:
{$subject}

Class / Level:
{$class}

Exam Type:
{$pastPaper->exam_type}

Exam Year:
{$pastPaper->exam_year}

Paper Title:
{$pastPaper->title}

EXAMINATION PAPER TEXT

{$text}

Identify every actual examinable question and sub-question.

For short-answer, note-taking, information-extraction, and other
questions worth multiple marks, identify the individual marking points
that a student's answer could earn.

Return these as a "marking_points" array.

Each marking point must represent one distinct piece of information
that can independently earn credit.

For example, if the expected answer is:

"You feel close to the audience; can walk to the theatre from his home;
the staff are friendly; his dressing room is very quiet."

return:

[
  "Close to the audience",
  "Can walk to the theatre from his home",
  "The staff are friendly",
  "His dressing room is very quiet"
]

Do not simply split an answer into arbitrary phrases.

For questions where there are no meaningful individual marking points,
return an empty array.

The number of marking points may be greater than the number of marks
available when the examination mark scheme allows alternative valid
points. The marks field remains the maximum number of marks available.

Important:

- Do not invent questions.
- Preserve question wording.
- Preserve question numbering.
- Include marks where present.
- Identify the topic, subtopic and concept.
- Estimate difficulty as easy, medium or hard.
- Identify the command word where applicable.
- Include answers only when they can reasonably be determined.
- Include explanations when they can reasonably be determined.
- Include all multiple-choice options.
- Do not treat instructions or reading passages as questions.

Return the required structured JSON only.
PROMPT;
    }

    /**
     * JSON Schema sent to Gemini.
     */
    protected function questionSchema(): array
    {
        return [
            'type' => 'object',

            'properties' => [
                'questions' => [
                    'type' => 'array',

                    'items' => [
                        'type' => 'object',

                        'properties' => [
                            'question_number' => [
                                'type' => 'string',
                                'description' =>
                                    'Original question number, for example 1, 7(a), or 7(i).',
                            ],

                            'section' => [
                                'type' => ['string', 'null'],
                                'description' =>
                                    'Section, exercise or paper section containing the question.',
                            ],

                            'question' => [
                                'type' => 'string',
                                'description' =>
                                    'The actual examinable question text.',
                            ],

                            'question_type' => [
                                'type' => 'string',
                                'enum' => [
                                    'multiple_choice',
                                    'short_answer',
                                    'structured',
                                    'extended_response',
                                    'true_false',
                                    'matching',
                                    'gap_fill',
                                    'writing',
                                    'calculation',
                                    'essay',
                                    'unknown',
                                ],
                            ],

                            'marks' => [
                                'type' => 'integer',
                                'minimum' => 1,
                            ],

                            'topic' => [
                                'type' => ['string', 'null'],
                            ],

                            'subtopic' => [
                                'type' => ['string', 'null'],
                            ],

                            'concept' => [
                                'type' => ['string', 'null'],
                            ],

                            'difficulty' => [
                                'type' => 'string',
                                'enum' => [
                                    'easy',
                                    'medium',
                                    'hard',
                                ],
                            ],

                            'command_word' => [
                                'type' => ['string', 'null'],
                            ],

                            'answer' => [
                                'type' => ['string', 'null'],
                            ],

                            'marking_points' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'string',
                                ],
                                'description' => 'Individual marking points required to earn marks. Use an empty array when the question does not require multiple marking points.',
                            ],

                            'explanation' => [
                                'type' => ['string', 'null'],
                            ],

                            'options' => [
                                'type' => 'array',

                                'items' => [
                                    'type' => 'object',

                                    'properties' => [
                                        'label' => [
                                            'type' => 'string',
                                        ],

                                        'option_text' => [
                                            'type' => 'string',
                                        ],

                                        'is_correct' => [
                                            'type' => 'boolean',
                                        ],
                                    ],

                                    'required' => [
                                        'label',
                                        'option_text',
                                        'is_correct',
                                    ],

                                    'additionalProperties' => false,
                                ],
                            ],
                        ],

                        'required' => [
                            'question_number',
                            'section',
                            'question',
                            'question_type',
                            'marks',
                            'topic',
                            'subtopic',
                            'concept',
                            'difficulty',
                            'command_word',
                            'answer',
                            'explanation',
                            'options',
                        ],

                        'additionalProperties' => false,
                    ],
                ],
            ],

            'required' => [
                'questions',
            ],

            'additionalProperties' => false,
        ];
    }

    /**
     * Parse and validate the structured AI response.
     */
    protected function parseResponse(string $response): array
    {
        $response = trim($response);

        /*
         * Structured output should already be JSON.
         *
         * This fallback handles an occasional Markdown fence
         * if a provider ignores the requested format.
         */
        if (str_starts_with($response, '```')) {
            $response = preg_replace(
                '/^```(?:json)?\s*/i',
                '',
                $response
            ) ?? $response;

            $response = preg_replace(
                '/\s*```$/',
                '',
                $response
            ) ?? $response;

            $response = trim($response);
        }

        $data = json_decode(
            $response,
            true
        );

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            ! is_array($data)
        ) {
            throw new RuntimeException(
                'The AI returned invalid JSON: ' .
                json_last_error_msg()
            );
        }

        if (
            ! isset($data['questions']) ||
            ! is_array($data['questions'])
        ) {
            throw new RuntimeException(
                'The AI response does not contain a valid questions array.'
            );
        }

        return $data['questions'];
    }

    /**
     * Store an analyzed question.
     */
    protected function storeQuestion(
        PastPaper $pastPaper,
        array $data
    ): Question {
        $question = Question::create([
            'past_paper_id' => $pastPaper->id,

            'subject_id' => $pastPaper->subject_id,

            'class_id' => $pastPaper->class_id,

            'topic' => $this->nullableString(
                $data['topic'] ?? null
            ),

            'subtopic' => $this->nullableString(
                $data['subtopic'] ?? null
            ),

            'concept' => $this->nullableString(
                $data['concept'] ?? null
            ),

            'question_type' => $this->normalizeQuestionType(
                $data['question_type'] ?? null
            ),

            'difficulty' => $this->normalizeDifficulty(
                $data['difficulty'] ?? null
            ),

            'command_word' => $this->nullableString(
                $data['command_word'] ?? null
            ),

            'question' => $this->cleanUtf8(
                (string) ($data['question'] ?? '')
            ),

            'marks' => max(
                1,
                (int) ($data['marks'] ?? 1)
            ),

            'answer' => $this->nullableString(
                $data['answer'] ?? null
            ),

            'explanation' => $this->nullableString(
                $data['explanation'] ?? null
            ),

            'source_type' => 'past_paper',

            /*
             * AI has identified the question, but an admin
             * must review it before it becomes usable.
             */
            'status' => 'pending_review',

            'ai_model' => config('services.ai.model'),

            'generation_notes' =>
                'Automatically analyzed from past paper.',

            'metadata' => [
                'question_number' =>
                    $data['question_number'] ?? null,

                'section' =>
                    $data['section'] ?? null,

                'marking_points' => array_values(
                    array_filter(
                        $data['marking_points'] ?? [],
                        fn ($point) =>
                            is_string($point) && trim($point) !== ''
                    )
                ),
            ],
        ]);

        /*
         * Save MCQ options.
         */
        $options = $data['options'] ?? [];

        if (is_array($options)) {
            foreach ($options as $index => $option) {
                if (! is_array($option)) {
                    continue;
                }

                $optionText = trim(
                    (string) ($option['option_text'] ?? '')
                );

                if ($optionText === '') {
                    continue;
                }

                QuestionOption::create([
                    'question_id' => $question->id,

                    'label' => strtoupper(
                        trim(
                            (string) (
                                $option['label']
                                ?? chr(65 + $index)
                            )
                        )
                    ),

                    'option_text' => $this->cleanUtf8(
                        $optionText
                    ),

                    'is_correct' => (bool) (
                        $option['is_correct'] ?? false
                    ),

                    'sort_order' => $index,
                ]);
            }
        }

        return $question;
    }

    protected function normalizeQuestionType(
        mixed $type
    ): string {
        $allowed = [
            'multiple_choice',
            'short_answer',
            'structured',
            'extended_response',
            'true_false',
            'matching',
            'gap_fill',
            'writing',
            'calculation',
            'essay',
            'unknown',
        ];

        $type = strtolower(
            trim((string) $type)
        );

        return in_array(
            $type,
            $allowed,
            true
        )
            ? $type
            : 'unknown';
    }

    protected function normalizeDifficulty(
        mixed $difficulty
    ): string {
        $difficulty = strtolower(
            trim((string) $difficulty)
        );

        return in_array(
            $difficulty,
            ['easy', 'medium', 'hard'],
            true
        )
            ? $difficulty
            : 'medium';
    }

    protected function nullableString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value = trim(
            $this->cleanUtf8((string) $value)
        );

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Make strings safe for MySQL utf8mb4.
     */
    protected function cleanUtf8(
        string $text
    ): string {
        $text = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $text
        ) ?? $text;

        if (! mb_check_encoding(
            $text,
            'UTF-8'
        )) {
            $converted = @mb_convert_encoding(
                $text,
                'UTF-8',
                'Windows-1252'
            );

            if ($converted !== false) {
                $text = $converted;
            }
        }

        $cleaned = @iconv(
            'UTF-8',
            'UTF-8//IGNORE',
            $text
        );

        if ($cleaned !== false) {
            $text = $cleaned;
        }

        return str_replace(
            "\0",
            '',
            $text
        );
    }
}
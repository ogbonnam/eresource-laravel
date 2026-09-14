<?php

namespace App\Services;

use App\Models\PastPaper;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MarkSchemeAnalyzer
{
    /**
     * Analyze an official mark scheme and return structured
     * machine-readable marking rules.
     */
    public function analyze(string $markSchemeText): array
    {
        $markSchemeText = $this->cleanText($markSchemeText);

        if ($markSchemeText === '') {
            throw new RuntimeException(
                'The mark scheme text is empty.'
            );
        }

        $response = Http::timeout(180)
            ->withHeaders([
                'Authorization' => 'Bearer ' . config('services.ai.key'),
                'Content-Type' => 'application/json',
            ])
            ->post(
                config('services.ai.endpoint'),
                [
                    'model' => config('services.ai.model'),

                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => $markSchemeText,
                        ],
                    ],

                    'temperature' => 0,

                    'response_format' => [
                        'type' => 'json_schema',

                        'json_schema' => [
                            'name' => 'mark_scheme',
                            'strict' => true,

                            'schema' => [
                                'type' => 'object',

                                'properties' => [

                                    /*
                                     * =================================================
                                     * PAPER
                                     * =================================================
                                     */
                                    'paper' => [
                                        'type' => 'object',

                                        'properties' => [
                                            'subject' => [
                                                'type' => [
                                                    'string',
                                                    'null',
                                                ],
                                            ],

                                            'paper_code' => [
                                                'type' => [
                                                    'string',
                                                    'null',
                                                ],
                                            ],

                                            'maximum_marks' => [
                                                'type' => [
                                                    'integer',
                                                    'null',
                                                ],
                                            ],
                                        ],

                                        'required' => [
                                            'subject',
                                            'paper_code',
                                            'maximum_marks',
                                        ],

                                        'additionalProperties' => false,
                                    ],

                                    /*
                                     * =================================================
                                     * QUESTIONS
                                     * =================================================
                                     */
                                    'questions' => [
                                        'type' => 'array',

                                        'items' => [
                                            'type' => 'object',

                                            'properties' => [

                                                'question_number' => [
                                                    'type' => 'string',
                                                ],

                                                'method' => [
                                                    'type' => 'string',

                                                    'enum' => [
                                                        'exact',
                                                        'multiple_choice',
                                                        'marking_points',
                                                        'levels',
                                                        'manual',
                                                    ],
                                                ],

                                                'maximum_marks' => [
                                                    'type' => 'integer',
                                                ],

                                                'answer' => [
                                                    'type' => [
                                                        'string',
                                                        'null',
                                                    ],
                                                ],

                                                'accepted_answers' => [
                                                    'type' => 'array',

                                                    'items' => [
                                                        'type' => 'string',
                                                    ],
                                                ],

                                                /*
                                                 * =================================================
                                                 * MARKING POINTS
                                                 * =================================================
                                                 */
                                                'marking_points' => [
                                                    'type' => 'array',

                                                    'items' => [
                                                        'type' => 'object',

                                                        'properties' => [

                                                            'criterion' => [
                                                                'type' => 'string',
                                                            ],

                                                            'marks' => [
                                                                'type' => 'integer',
                                                            ],

                                                            'accepted_ideas' => [
                                                                'type' => 'array',

                                                                'items' => [
                                                                    'type' => 'string',
                                                                ],
                                                            ],

                                                            'restrictions' => [
                                                                'type' => 'array',

                                                                'items' => [
                                                                    'type' => 'string',
                                                                ],
                                                            ],
                                                        ],

                                                        'required' => [
                                                            'criterion',
                                                            'marks',
                                                            'accepted_ideas',
                                                            'restrictions',
                                                        ],

                                                        'additionalProperties' => false,
                                                    ],
                                                ],

                                                /*
                                                 * =================================================
                                                 * LEVELS
                                                 * =================================================
                                                 */
                                                'content_max' => [
                                                    'type' => [
                                                        'integer',
                                                        'null',
                                                    ],
                                                ],

                                                'language_max' => [
                                                    'type' => [
                                                        'integer',
                                                        'null',
                                                    ],
                                                ],

                                                'content_levels' => [
                                                    'type' => 'array',

                                                    'items' => [
                                                        'type' => 'object',

                                                        'properties' => [
                                                            'min' => [
                                                                'type' => 'integer',
                                                            ],

                                                            'max' => [
                                                                'type' => 'integer',
                                                            ],

                                                            'description' => [
                                                                'type' => 'string',
                                                            ],
                                                        ],

                                                        'required' => [
                                                            'min',
                                                            'max',
                                                            'description',
                                                        ],

                                                        'additionalProperties' => false,
                                                    ],
                                                ],

                                                'language_levels' => [
                                                    'type' => 'array',

                                                    'items' => [
                                                        'type' => 'object',

                                                        'properties' => [
                                                            'min' => [
                                                                'type' => 'integer',
                                                            ],

                                                            'max' => [
                                                                'type' => 'integer',
                                                            ],

                                                            'description' => [
                                                                'type' => 'string',
                                                            ],
                                                        ],

                                                        'required' => [
                                                            'min',
                                                            'max',
                                                            'description',
                                                        ],

                                                        'additionalProperties' => false,
                                                    ],
                                                ],

                                                /*
                                                 * =================================================
                                                 * NUMERIC RANGE RULES
                                                 *
                                                 * This prevents important ranges such as
                                                 * "1–2 marks" from becoming "12 marks".
                                                 * =================================================
                                                 */
                                                'mark_range_rules' => [
                                                    'type' => 'array',

                                                    'items' => [
                                                        'type' => 'object',

                                                        'properties' => [

                                                            'criterion' => [
                                                                'type' => 'string',
                                                            ],

                                                            'minimum' => [
                                                                'type' => 'integer',
                                                            ],

                                                            'maximum' => [
                                                                'type' => 'integer',
                                                            ],

                                                            'description' => [
                                                                'type' => 'string',
                                                            ],
                                                        ],

                                                        'required' => [
                                                            'criterion',
                                                            'minimum',
                                                            'maximum',
                                                            'description',
                                                        ],

                                                        'additionalProperties' => false,
                                                    ],
                                                ],

                                                /*
                                                 * =================================================
                                                 * SPECIAL RULES
                                                 * =================================================
                                                 */
                                                'special_rules' => [
                                                    'type' => 'array',

                                                    'items' => [
                                                        'type' => 'string',
                                                    ],
                                                ],

                                                'notes' => [
                                                    'type' => 'array',

                                                    'items' => [
                                                        'type' => 'string',
                                                    ],
                                                ],
                                            ],

                                            'required' => [
                                                'question_number',
                                                'method',
                                                'maximum_marks',
                                                'answer',
                                                'accepted_answers',
                                                'marking_points',
                                                'content_max',
                                                'language_max',
                                                'content_levels',
                                                'language_levels',
                                                'mark_range_rules',
                                                'special_rules',
                                                'notes',
                                            ],

                                            'additionalProperties' => false,
                                        ],
                                    ],

                                    /*
                                     * =================================================
                                     * GENERAL RULES
                                     * =================================================
                                     */
                                    'general_rules' => [
                                        'type' => 'array',

                                        'items' => [
                                            'type' => 'string',
                                        ],
                                    ],
                                ],

                                'required' => [
                                    'paper',
                                    'questions',
                                    'general_rules',
                                ],

                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Mark scheme AI request failed: '
                . $response->status()
                . ' '
                . $response->body()
            );
        }

        $json = $response->json();

        $content = data_get(
            $json,
            'choices.0.message.content'
        );

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException(
                'Gemini returned an empty mark scheme response.'
            );
        }

        $content = $this->stripMarkdownFences($content);

        $result = json_decode(
            $content,
            true
        );

        if (! is_array($result)) {
            throw new RuntimeException(
                'Gemini returned invalid mark scheme JSON: '
                . json_last_error_msg()
            );
        }

        return $this->normalizeResult($result);
    }

    /**
     * Attach analyzed marking rules to questions belonging
     * to a past paper.
     *
     * Validation happens BEFORE any question is modified.
     *
     * @return int Number of updated questions.
     */
    public function attachToPastPaper(
        PastPaper $pastPaper,
        array $markScheme
    ): int {
        $validation = $this->validateMarkScheme(
            $pastPaper,
            $markScheme
        );

        if (! $validation['valid']) {
            throw new RuntimeException(
                "Mark scheme validation failed.\n\n"
                . $this->formatValidationErrors($validation)
            );
        }

        $questions = $pastPaper->questions()->get();

        $rules = collect(
            $markScheme['questions'] ?? []
        )->keyBy(function (array $rule) {
            return $this->normalizeQuestionNumber(
                $rule['question_number'] ?? ''
            );
        });

        $updated = 0;

        foreach ($questions as $question) {
            $questionNumber = $this->getQuestionNumber(
                $question
            );

            if ($questionNumber === null) {
                continue;
            }

            $normalizedNumber = $this->normalizeQuestionNumber(
                $questionNumber
            );

            $rule = $rules->get(
                $normalizedNumber
            );

            if (! is_array($rule)) {
                continue;
            }

            $metadata = $question->metadata ?? [];

            $metadata['marking'] = $rule;

            $question->metadata = $metadata;

            $question->save();

            $updated++;
        }

        return $updated;
    }

    /**
     * Validate the analyzed mark scheme against the actual
     * questions belonging to the past paper.
     */
    public function validateMarkScheme(
        PastPaper $pastPaper,
        array $markScheme
    ): array {
        $questions = $pastPaper->questions()
            ->orderBy('id')
            ->get();

        $rules = collect(
            $markScheme['questions'] ?? []
        );

        $rulesByNumber = $rules->keyBy(
            fn (array $rule) =>
                $this->normalizeQuestionNumber(
                    $rule['question_number'] ?? ''
                )
        );

        $paperNumbers = [];

        $missing = [];

        $malformed = [];

        $validated = [];

        foreach ($questions as $question) {
            $questionNumber = $this->getQuestionNumber(
                $question
            );

            if ($questionNumber === null) {
                $malformed[] = [
                    'question_number' => null,
                    'errors' => [
                        'Question has no question_number in metadata.',
                    ],
                ];

                continue;
            }

            $normalizedNumber =
                $this->normalizeQuestionNumber(
                    $questionNumber
                );

            $paperNumbers[] = $normalizedNumber;

            if (! $rulesByNumber->has($normalizedNumber)) {
                $missing[] = $normalizedNumber;

                continue;
            }

            $rule = $rulesByNumber->get(
                $normalizedNumber
            );

            $errors = $this->validateRule(
                $rule,
                $question
            );

            if ($errors !== []) {
                $malformed[] = [
                    'question_number' => $normalizedNumber,
                    'errors' => $errors,
                ];

                continue;
            }

            $validated[] = $normalizedNumber;
        }

        /*
         * Detect rules Gemini produced that do not belong
         * to an actual question in this paper.
         */
        $extra = $rulesByNumber
            ->keys()
            ->filter(
                fn ($number) =>
                    ! in_array(
                        $number,
                        $paperNumbers,
                        true
                    )
            )
            ->values()
            ->all();

        return [
            'valid' =>
                empty($missing)
                && empty($malformed)
                && empty($extra),

            'question_count' => count($paperNumbers),

            'rule_count' => $rulesByNumber->count(),

            'validated_count' => count($validated),

            'missing' => array_values(
                array_unique($missing)
            ),

            'malformed' => $malformed,

            'extra' => $extra,
        ];
    }

    /**
     * Validate an individual marking rule.
     */
    protected function validateRule(
        array $rule,
        $question = null
    ): array {
        $errors = [];

        $method = $rule['method'] ?? null;

        if (blank($method)) {
            $errors[] = 'Missing marking method.';
        }

        $maximumMarks =
            $rule['maximum_marks'] ?? null;

        if (
            ! is_int($maximumMarks)
            && ! is_numeric($maximumMarks)
        ) {
            $errors[] =
                'Missing or invalid maximum_marks.';
        }

        if (
            is_numeric($maximumMarks)
            && (int) $maximumMarks < 0
        ) {
            $errors[] =
                'maximum_marks cannot be negative.';
        }

        /*
         * Exact questions need an answer unless the
         * mark scheme genuinely provides no automatic answer.
         */
        if (
            $method === 'exact'
            && blank($rule['answer'])
            && empty($rule['accepted_answers'])
        ) {
            $errors[] =
                'Exact question has no answer or accepted answers.';
        }

        /*
         * MCQs must have an answer.
         */
        if (
            $method === 'multiple_choice'
            && blank($rule['answer'])
        ) {
            $errors[] =
                'Multiple-choice question has no answer.';
        }

        /*
         * Marking-point questions must contain at least
         * one point.
         */
        if (
            $method === 'marking_points'
            && empty($rule['marking_points'])
        ) {
            $errors[] =
                'Marking-points question has no marking points.';
        }

        /*
         * Levels-based questions need both sets of levels.
         */
        if ($method === 'levels') {

            if (
                empty($rule['content_levels'])
            ) {
                $errors[] =
                    'Levels question has no content levels.';
            }

            if (
                empty($rule['language_levels'])
            ) {
                $errors[] =
                    'Levels question has no language levels.';
            }

            if (
                $rule['content_max'] === null
                && $rule['language_max'] === null
            ) {
                $errors[] =
                    'Levels question has no content_max or language_max.';
            }
        }

        /*
         * Validate individual marking points.
         */
        foreach (
            $rule['marking_points'] ?? []
            as $index => $point
        ) {
            if (
                blank($point['criterion'] ?? null)
            ) {
                $errors[] =
                    "Marking point {$index} has no criterion.";
            }

            if (
                ! isset($point['marks'])
                || ! is_numeric($point['marks'])
                || (int) $point['marks'] < 0
            ) {
                $errors[] =
                    "Marking point {$index} has invalid marks.";
            }
        }

        /*
         * Validate explicit numeric ranges.
         */
        foreach (
            $rule['mark_range_rules'] ?? []
            as $index => $range
        ) {
            $minimum = $range['minimum'] ?? null;
            $maximum = $range['maximum'] ?? null;

            if (
                ! is_int($minimum)
                || ! is_int($maximum)
            ) {
                $errors[] =
                    "Mark range {$index} has invalid minimum/maximum.";
            }

            if (
                is_int($minimum)
                && is_int($maximum)
                && $minimum > $maximum
            ) {
                $errors[] =
                    "Mark range {$index} has minimum greater than maximum.";
            }
        }

        /*
         * Detect the exact AI error we previously encountered:
         *
         * 1–2 marks
         *
         * being incorrectly represented as:
         *
         * 12 marks
         */
        foreach (
            $rule['special_rules'] ?? []
            as $specialRule
        ) {
            if (! is_string($specialRule)) {
                continue;
            }

            if (
                preg_match(
                    '/\b(?:given|award|awarded|maximum|max|up to)\s+12\s+marks?\b/i',
                    $specialRule
                )
            ) {
                $errors[] =
                    'Suspicious "12 marks" value detected. '
                    . 'This may be an incorrectly parsed 1–2 mark range.';
            }

            if (
                preg_match(
                    '/\bbelow\s+85\s+words\b/i',
                    $specialRule)
                && preg_match(
                    '/\b12\s+marks?\b/i',
                    $specialRule
                )
            ) {
                $errors[] =
                    'The below-85-word rule appears to contain '
                    . '"12 marks". Expected a 1–2 Content mark range.';
            }
        }

        return array_values(
            array_unique($errors)
        );
    }

    /**
     * Return the question number stored by
     * PastPaperQuestionAnalyzer.
     */
    protected function getQuestionNumber(
        $question
    ): ?string {
        $metadata = $question->metadata ?? [];

        $number =
            $metadata['question_number']
            ?? null;

        if (
            $number === null
            || trim((string) $number) === ''
        ) {
            return null;
        }

        return trim((string) $number);
    }

    /**
     * Normalize question numbers so values such as:
     *
     * Q7(a)
     * 7(a)
     * Q7a
     * 7a
     *
     * can be compared consistently.
     */
    protected function normalizeQuestionNumber(
        string $number
    ): string {
        $number = trim($number);

        $number = preg_replace(
            '/^q(?:uestion)?\s*/i',
            '',
            $number
        );

        $number = preg_replace(
            '/\s+/',
            '',
            $number
        );

        $number = str_replace(
            ['[', ']', '{', '}'],
            '',
            $number
        );

        return strtolower($number);
    }

    /**
     * Normalize Gemini's result and guarantee that
     * every expected field exists.
     */
    protected function normalizeResult(
        array $result
    ): array {
        $result['paper'] ??= [];

        $result['paper']['subject'] ??= null;

        $result['paper']['paper_code'] ??= null;

        $result['paper']['maximum_marks'] ??= null;

        $result['questions'] ??= [];

        $result['general_rules'] ??= [];

        foreach (
            $result['questions']
            as &$question
        ) {
            $question['question_number'] ??= '';

            $question['method'] ??= 'manual';

            $question['maximum_marks'] ??= 0;

            $question['answer'] ??= null;

            $question['accepted_answers'] ??= [];

            $question['marking_points'] ??= [];

            $question['content_max'] ??= null;

            $question['language_max'] ??= null;

            $question['content_levels'] ??= [];

            $question['language_levels'] ??= [];

            $question['mark_range_rules'] ??= [];

            $question['special_rules'] ??= [];

            $question['notes'] ??= [];

            /*
             * Normalize marking points.
             */
            foreach (
                $question['marking_points']
                as &$point
            ) {
                $point['criterion'] ??= '';

                $point['marks'] ??= 1;

                $point['accepted_ideas'] ??= [];

                $point['restrictions'] ??= [];
            }

            unset($point);

            /*
             * Normalize explicit numeric ranges.
             */
            foreach (
                $question['mark_range_rules']
                as &$range
            ) {
                $range['criterion'] ??= '';

                $range['minimum'] ??= 0;

                $range['maximum'] ??= 0;

                $range['description'] ??= '';
            }

            unset($range);

            /*
             * Normalize levels.
             */
            foreach (
                $question['content_levels']
                as &$level
            ) {
                $level['min'] ??= 0;

                $level['max'] ??= 0;

                $level['description'] ??= '';
            }

            unset($level);

            foreach (
                $question['language_levels']
                as &$level
            ) {
                $level['min'] ??= 0;

                $level['max'] ??= 0;

                $level['description'] ??= '';
            }

            unset($level);
        }

        unset($question);

        return $result;
    }

    /**
     * Clean extracted PDF text without changing its meaning.
     */
    protected function cleanText(
        string $text
    ): string {
        /*
         * Soft hyphen.
         */
        $text = str_replace(
            "\xC2\xAD",
            '',
            $text
        );

        $text = str_replace(
            "\u{00AD}",
            '',
            $text
        );

        /*
         * Normalize non-breaking spaces.
         */
        $text = str_replace(
            "\xC2\xA0",
            ' ',
            $text
        );

        /*
         * Normalize whitespace.
         */
        $text = preg_replace(
            '/[ \t]+/',
            ' ',
            $text
        );

        /*
         * Prevent huge blank-line blocks.
         */
        $text = preg_replace(
            "/\n{3,}/",
            "\n\n",
            $text
        );

        return trim($text);
    }

    /**
     * Remove optional Markdown JSON fences.
     */
    protected function stripMarkdownFences(
        string $content
    ): string {
        $content = trim($content);

        if (
            str_starts_with(
                $content,
                '```'
            )
            && str_ends_with(
                $content,
                '```'
            )
        ) {
            $content = preg_replace(
                '/^```(?:json)?\s*/i',
                '',
                $content
            );

            $content = preg_replace(
                '/\s*```$/',
                '',
                $content
            );
        }

        return trim($content);
    }

    /**
     * Format validation errors for the Filament notification
     * / exception.
     */
    protected function formatValidationErrors(
        array $validation
    ): string {
        $lines = [];

        $lines[] =
            'Questions found: '
            . $validation['question_count'];

        $lines[] =
            'Marking rules found: '
            . $validation['rule_count'];

        if (
            ! empty($validation['missing'])
        ) {
            $lines[] =
                'Missing rules: '
                . implode(
                    ', ',
                    $validation['missing']
                );
        }

        if (
            ! empty($validation['extra'])
        ) {
            $lines[] =
                'Extra rules: '
                . implode(
                    ', ',
                    $validation['extra']
                );
        }

        if (
            ! empty($validation['malformed'])
        ) {
            $lines[] =
                'Malformed rules:';

            foreach (
                $validation['malformed']
                as $item
            ) {
                $questionNumber =
                    $item['question_number']
                    ?? 'unknown';

                foreach (
                    $item['errors']
                    ?? []
                    as $error
                ) {
                    $lines[] =
                        "Q{$questionNumber}: {$error}";
                }
            }
        }

        return implode(
            "\n",
            $lines
        );
    }

    /**
     * Gemini system prompt.
     */
    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert examination mark-scheme analyst.

Your task is to convert an OFFICIAL examination mark scheme into
machine-readable marking rules for an educational assessment system.

The official mark scheme is the ONLY authority.

Do not invent, infer, improve, broaden, or rewrite the official marking
criteria.

============================================================
CORE RULES
============================================================

1. The official mark scheme is authoritative.

2. Extract every question that has a marking rule.

3. Do NOT omit a question.

4. Question numbers MUST be preserved exactly enough to match the
   corresponding question in the paper.

5. Do not invent answers, criteria, synonyms, restrictions, marks,
   scoring rules, or exceptions.

6. Do not turn general knowledge into an accepted answer unless the
   official mark scheme explicitly supports it.

7. Preserve the official maximum marks.

8. Preserve all official restrictions.

9. Preserve all official spelling requirements.

10. Preserve all official grammar requirements.

11. Preserve all official word-count rules.

12. Preserve all official content/language limits.

============================================================
QUESTION METHODS
============================================================

Use:

"exact"

for a question with a single exact answer or explicitly stated
accepted answer.

Use:

"multiple_choice"

for multiple-choice questions where the mark scheme gives the
correct option.

Use:

"marking_points"

for questions where candidates can earn marks independently for
individual ideas or points.

Use:

"levels"

for questions assessed using levels/descriptors, such as writing
questions with separate Content and Language marks.

Use:

"manual"

only when the official mark scheme does not provide enough
information for reliable automated marking.

============================================================
MULTIPLE CHOICE
============================================================

If the official mark scheme gives answers such as:

Q7(a) D
Q7(b) A
Q7(c) C

the method MUST be:

"multiple_choice"

The answer MUST contain the official option, for example:

"D"

Do not classify these as "exact" merely because the answer is a
single letter.

============================================================
MARKING POINTS
============================================================

For marking-point questions:

- Extract every official marking point.
- Preserve all points even when there are more points than the
  maximum marks.
- The maximum_marks field controls the maximum score.
- Do not remove alternative acceptable points.
- Do not create additional synonyms.
- Only include accepted ideas explicitly supported by the official
  mark scheme.
- Preserve restrictions exactly.

For example, if four possible points are given but the question
maximum is three marks, store all four points and maximum_marks = 3.

============================================================
NUMERIC RANGES — CRITICAL
============================================================

NEVER concatenate the endpoints of a numeric range.

For example:

"1–2 marks"

MUST NEVER become:

"12 marks"

A range MUST be represented using numeric fields:

minimum = 1
maximum = 2

Use mark_range_rules when a numeric range is important to the
marking rule.

For example:

{
    "criterion": "Content marks for responses below 85 words",
    "minimum": 1,
    "maximum": 2,
    "description": "Below 85 words, award Content marks only in the 1–2 range."
}

Do not convert:

1–2

into:

12

Do not convert:

5–6

into:

56

Do not convert:

7–9

into:

79

Preserve numeric ranges exactly.

============================================================
WRITING / LEVELS
============================================================

For levels-based questions:

- Use method "levels".
- Preserve Content maximum.
- Preserve Language maximum.
- Preserve every official Content level.
- Preserve every official Language level.
- Preserve every special rule.
- Preserve word-count restrictions.
- Preserve bullet-point omission rules.
- Preserve relevance rules.
- Preserve instructions about Content and Language scoring.

Do NOT invent a numerical score from a descriptor.

Example:

If the official mark scheme says:

"Below 85 words: 1–2 Content marks only"

then:

content_max should NOT become 12.

Instead use mark_range_rules:

criterion:
"Content marks for responses below 85 words"

minimum:
1

maximum:
2

description:
"Below 85 words, Content is limited to the 1–2 mark range."

Also preserve the rule in special_rules if useful.

============================================================
ACCEPTED ANSWERS
============================================================

Only include accepted_answers that are explicitly supported by the
official mark scheme.

Do not create a large synonym dictionary.

For example, if the mark scheme explicitly accepts:

"tons"

alongside:

"tonnes"

then both may be stored.

If the mark scheme says that "tones" is not accepted, preserve that
restriction.

============================================================
RESTRICTIONS
============================================================

Restrictions are extremely important.

Examples include:

- idea must be conveyed
- walking idea is essential
- plural idea required
- specific spelling not accepted
- alternative word changes meaning
- one particular interpretation is not acceptable
- a bullet point must be addressed
- maximum Content marks
- word-count restrictions

Do not lose restrictions.

============================================================
NO INVENTION
============================================================

Do not add synonyms because they appear linguistically reasonable.

Do not add alternative answers because you believe they have the same
meaning.

Do not create marking points that are not present.

The system may later use semantic evaluation for ambiguous responses.

Your job is to faithfully preserve the official mark scheme.

============================================================
COMPLETENESS
============================================================

Every question present in the official mark scheme must appear in
the questions array.

Do not silently omit questions.

If a question genuinely cannot be automatically interpreted, include
it with method:

"manual"

rather than omitting it.

============================================================
OUTPUT
============================================================

Return ONLY the requested JSON structure.

Do not return Markdown.

Do not return explanations outside the JSON structure.

Do not add fields that are not present in the schema.

PROMPT;
    }
}
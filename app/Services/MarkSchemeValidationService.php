<?php

namespace App\Services;

use App\Models\PastPaper;

class MarkSchemeValidationService
{
    public function validate(
        PastPaper $pastPaper,
        array $markScheme
    ): array {
        $questions = $pastPaper->questions()
            ->orderBy('id')
            ->get();

        $rules = collect($markScheme['questions'] ?? [])
            ->keyBy(
                fn (array $rule) =>
                    $this->normalizeQuestionNumber(
                        $rule['question_number'] ?? ''
                    )
            );

        $missing = [];
        $malformed = [];
        $validated = [];

        foreach ($questions as $question) {
            $questionNumber = $this->normalizeQuestionNumber(
                data_get($question->metadata, 'question_number', '')
            );

            if ($questionNumber === '') {
                $malformed[] = [
                    'question_id' => $question->id,
                    'reason' => 'Question has no question number.',
                ];

                continue;
            }

            if (! $rules->has($questionNumber)) {
                $missing[] = $questionNumber;

                continue;
            }

            $rule = $rules->get($questionNumber);

            $errors = $this->validateRule($rule);

            if ($errors !== []) {
                $malformed[] = [
                    'question_number' => $questionNumber,
                    'errors' => $errors,
                ];

                continue;
            }

            $validated[] = $questionNumber;
        }

        $paperQuestionNumbers = $questions
            ->map(fn ($question) =>
                $this->normalizeQuestionNumber(
                    data_get($question->metadata, 'question_number', '')
                )
            )
            ->filter()
            ->values()
            ->all();

        $extra = $rules
            ->keys()
            ->filter(
                fn ($number) =>
                    ! in_array(
                        $number,
                        $paperQuestionNumbers,
                        true
                    )
            )
            ->values()
            ->all();

        return [
            'valid' => empty($missing)
                && empty($malformed),

            'question_count' => count($paperQuestionNumbers),

            'rule_count' => $rules->count(),

            'validated_count' => count($validated),

            'missing' => array_values($missing),

            'malformed' => array_values($malformed),

            'extra' => $extra,
        ];
    }

    protected function validateRule(array $rule): array
    {
        $errors = [];

        $method = $rule['method'] ?? null;

        if (blank($method)) {
            $errors[] = 'Missing marking method.';
        }

        $maximumMarks = $rule['maximum_marks'] ?? null;

        if (
            ! is_int($maximumMarks)
            && ! is_numeric($maximumMarks)
        ) {
            $errors[] = 'Missing or invalid maximum_marks.';
        }

        if (
            $method === 'multiple_choice'
            && blank($rule['answer'])
        ) {
            $errors[] = 'Multiple-choice question has no answer.';
        }

        if (
            $method === 'marking_points'
            && empty($rule['marking_points'])
        ) {
            $errors[] = 'Marking-points question has no marking points.';
        }

        if ($method === 'levels') {
            if (empty($rule['content_levels'])) {
                $errors[] = 'Levels question has no content levels.';
            }

            if (empty($rule['language_levels'])) {
                $errors[] = 'Levels question has no language levels.';
            }
        }

        /*
         * Prevent a known AI parsing error where:
         *
         * 1–2 marks
         *
         * becomes:
         *
         * 12 marks
         */
        foreach ($rule['special_rules'] ?? [] as $specialRule) {
            if (
                is_string($specialRule)
                && preg_match(
                    '/\b(?:given|award|awarded|marks?)\s+12\b/i',
                    $specialRule
                )
            ) {
                $errors[] =
                    'Suspicious "12 marks" value detected; ' .
                    'possible 1–2 mark range parsing error.';
            }
        }

        return $errors;
    }

    protected function normalizeQuestionNumber(
        mixed $number
    ): string {
        $number = trim((string) $number);

        $number = preg_replace(
            '/^(question|q)\s*/i',
            '',
            $number
        );

        $number = preg_replace(
            '/\s+/',
            '',
            $number
        );

        return strtoupper($number);
    }
}
<?php

namespace App\Console\Commands;

use App\Models\PastPaper;
use Illuminate\Console\Command;

class VerifyPastPaperQuestions extends Command
{
    protected $signature = 'questions:verify-past-paper
                            {pastPaper : Past paper ID}';

    protected $description = 'Verify questions extracted from a past paper';

    public function handle(): int
    {
        $pastPaper = PastPaper::with([
            'questions' => fn ($query) =>
                $query->orderBy('id'),
        ])->find($this->argument('pastPaper'));

        if (! $pastPaper) {
            $this->error('Past paper not found.');

            return self::FAILURE;
        }

        $questions = $pastPaper->questions;

        $this->newLine();

        $this->info("Past Paper: {$pastPaper->title}");
        $this->info("Status: {$pastPaper->status}");
        $this->info("Questions: {$questions->count()}");

        $this->newLine();

        $rows = [];

        foreach ($questions as $question) {
            $metadata = $question->metadata ?? [];

            $rows[] = [
                $metadata['question_number'] ?? '-',
                $metadata['section'] ?? '-',
                $question->question_type,
                $question->marks,
                $question->status,
                str($question->question)
                    ->replace(["\r", "\n"], ' ')
                    ->limit(70),
            ];
        }

        $this->table(
            [
                'Question',
                'Section',
                'Type',
                'Marks',
                'Status',
                'Question Text',
            ],
            $rows
        );

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | EXPECTED CAMBRIDGE STRUCTURE
        |--------------------------------------------------------------------------
        */

        $expected = [
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',

            '7(a)',
            '7(b)',
            '7(c)',
            '7(d)',
            '7(e)',
            '7(f)',
            '7(g)',
            '7(h)',
            '7(i)',

            '8',
            '9',

            '10',
            '11',
            '12',
            '13',
            '14',
            '15',

            '16',
            '17',
        ];

        $actual = $questions
            ->map(fn ($question) =>
                $question->metadata['question_number'] ?? null
            )
            ->filter()
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | CHECK COUNT
        |--------------------------------------------------------------------------
        */

        if ($questions->count() !== count($expected)) {
            $this->error(
                "Expected " . count($expected) .
                " questions but found {$questions->count()}."
            );

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK QUESTION NUMBERS
        |--------------------------------------------------------------------------
        */

        $missing = array_values(
            array_diff($expected, $actual)
        );

        $unexpected = array_values(
            array_diff($actual, $expected)
        );

        if ($missing) {
            $this->error(
                'Missing question numbers: ' .
                implode(', ', $missing)
            );

            return self::FAILURE;
        }

        if ($unexpected) {
            $this->error(
                'Unexpected question numbers: ' .
                implode(', ', $unexpected)
            );

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK MCQs
        |--------------------------------------------------------------------------
        */

        $mcqNumbers = [
            '10',
            '11',
            '12',
            '13',
            '14',
            '15',
        ];

        foreach ($mcqNumbers as $number) {

            $question = $questions->first(
                fn ($question) =>
                    ($question->metadata['question_number'] ?? null)
                    === $number
            );

            if (! $question) {
                continue;
            }

            $optionCount = $question->options()->count();

            if ($optionCount !== 3) {
                $this->error(
                    "Q{$number}: expected 3 options, found {$optionCount}."
                );

                return self::FAILURE;
            }

            $correctCount = $question
                ->options()
                ->where('is_correct', true)
                ->count();

            if ($correctCount !== 1) {
                $this->error(
                    "Q{$number}: expected exactly 1 correct option, found {$correctCount}."
                );

                return self::FAILURE;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK WRITING QUESTIONS
        |--------------------------------------------------------------------------
        */

        foreach (['16', '17'] as $number) {

            $question = $questions->first(
                fn ($question) =>
                    ($question->metadata['question_number'] ?? null)
                    === $number
            );

            if (! $question) {
                continue;
            }

            if (! in_array(
                $question->question_type,
                ['writing', 'email', 'essay'],
                true
            )) {
                $this->error(
                    "Q{$number}: expected writing question type."
                );

                return self::FAILURE;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK STATUS
        |--------------------------------------------------------------------------
        */

        $pending = $questions
            ->where('status', 'pending_review')
            ->count();

        $this->newLine();

        $this->info(
            "Pending Review: {$pending}"
        );

        if ($pending !== $questions->count()) {
            $this->warn(
                'Not all questions are currently pending review.'
            );
        }

        $this->newLine();

        $this->info(
            'SUCCESS: Past paper question structure passed verification.'
        );

        return self::SUCCESS;
    }
}
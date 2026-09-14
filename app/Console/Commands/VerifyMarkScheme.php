<?php

namespace App\Console\Commands;

use App\Models\PastPaper;
use Illuminate\Console\Command;

class VerifyMarkScheme extends Command
{
    protected $signature = 'questions:verify-marking
                            {past_paper : Past paper ID}';

    protected $description = 'Verify AI-generated mark scheme rules attached to past paper questions';

    public function handle(): int
    {
        $pastPaper = PastPaper::with([
            'questions' => fn ($query) => $query->orderBy('id'),
        ])->find($this->argument('past_paper'));

        if (! $pastPaper) {
            $this->error('Past paper not found.');

            return self::FAILURE;
        }

        $questions = $pastPaper->questions;

        $this->newLine();

        $this->info('Mark Scheme Verification');
        $this->line(str_repeat('=', 80));

        $this->line("Past Paper: {$pastPaper->title}");
        $this->line("Subject: " . ($pastPaper->subject?->name ?? '-'));
        $this->line("Questions: {$questions->count()}");

        $this->newLine();

        $withMarking = 0;
        $withoutMarking = 0;

        foreach ($questions as $question) {
            $metadata = $question->metadata ?? [];
            $marking = $metadata['marking'] ?? null;

            if (is_array($marking) && $marking !== []) {
                $withMarking++;
            } else {
                $withoutMarking++;
            }

            $questionNumber =
                $metadata['question_number']
                ?? $question->id;

            $this->line(
                str_repeat('-', 80)
            );

            $this->info(
                "QUESTION {$questionNumber}"
            );

            $this->line(
                "Question: " . $question->question
            );

            $this->line(
                "Stored marks: " . $question->marks
            );

            if (! is_array($marking) || $marking === []) {
                $this->warn(
                    'MARKING: No marking rule attached'
                );

                continue;
            }

            $method = $marking['method'] ?? 'unknown';
            $maximumMarks = $marking['maximum_marks'] ?? null;

            $this->newLine();

            $this->comment(
                "Marking method: {$method}"
            );

            $this->comment(
                'Maximum marks: ' .
                ($maximumMarks ?? '-')
            );

            /*
             * Exact / MCQ
             */
            if (
                in_array(
                    $method,
                    ['exact', 'multiple_choice'],
                    true
                )
            ) {
                if (! empty($marking['answer'])) {
                    $this->line(
                        'Answer: ' . $marking['answer']
                    );
                }

                if (! empty($marking['accepted_answers'])) {
                    $this->line('Accepted answers:');

                    foreach (
                        $marking['accepted_answers']
                        as $answer
                    ) {
                        $this->line(
                            "  - {$answer}"
                        );
                    }
                }
            }

            /*
             * Marking points
             */
            if (
                $method === 'marking_points'
                && ! empty($marking['marking_points'])
            ) {
                $this->line('Marking points:');

                foreach (
                    $marking['marking_points']
                    as $index => $point
                ) {
                    $criterion =
                        $point['criterion']
                        ?? '';

                    $marks =
                        $point['marks']
                        ?? 1;

                    $this->line(
                        sprintf(
                            '  %d. [%d mark] %s',
                            $index + 1,
                            $marks,
                            $criterion
                        )
                    );

                    if (
                        ! empty(
                            $point['accepted_ideas']
                        )
                    ) {
                        $this->line(
                            '     Accepted ideas: ' .
                            implode(
                                '; ',
                                $point['accepted_ideas']
                            )
                        );
                    }

                    if (
                        ! empty(
                            $point['restrictions']
                        )
                    ) {
                        $this->line(
                            '     Restrictions: ' .
                            implode(
                                '; ',
                                $point['restrictions']
                            )
                        );
                    }
                }
            }

            /*
             * Levels-based marking
             */
            if ($method === 'levels') {
                if (
                    ! empty(
                        $marking['content_max']
                    )
                ) {
                    $this->line(
                        'Content maximum: ' .
                        $marking['content_max']
                    );
                }

                if (
                    ! empty(
                        $marking['language_max']
                    )
                ) {
                    $this->line(
                        'Language maximum: ' .
                        $marking['language_max']
                    );
                }

                if (
                    ! empty(
                        $marking['content_levels']
                    )
                ) {
                    $this->line(
                        'Content levels:'
                    );

                    foreach (
                        $marking['content_levels']
                        as $level
                    ) {
                        $this->line(
                            sprintf(
                                '  %d-%d: %s',
                                $level['min'] ?? 0,
                                $level['max'] ?? 0,
                                $level['description'] ?? ''
                            )
                        );
                    }
                }

                if (
                    ! empty(
                        $marking['language_levels']
                    )
                ) {
                    $this->line(
                        'Language levels:'
                    );

                    foreach (
                        $marking['language_levels']
                        as $level
                    ) {
                        $this->line(
                            sprintf(
                                '  %d-%d: %s',
                                $level['min'] ?? 0,
                                $level['max'] ?? 0,
                                $level['description'] ?? ''
                            )
                        );
                    }
                }
            }

            /*
             * Special rules
             */
            if (
                ! empty(
                    $marking['special_rules']
                )
            ) {
                $this->line('Special rules:');

                foreach (
                    $marking['special_rules']
                    as $rule
                ) {
                    $this->line(
                        "  - {$rule}"
                    );
                }
            }

            /*
             * Notes
             */
            if (
                ! empty(
                    $marking['notes']
                )
            ) {
                $this->line('Notes:');

                foreach (
                    $marking['notes']
                    as $note
                ) {
                    $this->line(
                        "  - {$note}"
                    );
                }
            }

            $this->newLine();
        }

        $this->line(str_repeat('=', 80));

        $this->info(
            "Questions with marking rules: {$withMarking}"
        );

        if ($withoutMarking > 0) {
            $this->warn(
                "Questions without marking rules: {$withoutMarking}"
            );
        } else {
            $this->info(
                'All questions have marking rules.'
            );
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
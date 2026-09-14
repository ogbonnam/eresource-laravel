<?php

namespace App\Console\Commands;

use App\Models\PastPaper;
use App\Models\Question;
use App\Models\QuestionSection;
use Illuminate\Console\Command;

class BuildPastPaperSections extends Command
{
    protected $signature = 'questions:build-sections
                            {pastPaper : The past paper ID}';

    protected $description = 'Create question sections from an analyzed past paper and link its questions';

    public function handle(): int
    {
        $pastPaperId = (int) $this->argument('pastPaper');

        $pastPaper = PastPaper::find($pastPaperId);

        if (! $pastPaper) {
            $this->error("Past paper {$pastPaperId} was not found.");

            return self::FAILURE;
        }

        if (! $pastPaper->extracted_text) {
            $this->error('This past paper has no extracted text.');

            return self::FAILURE;
        }

        $this->info("Building sections for: {$pastPaper->title}");

        $sections = [
            [
                'section_key' => 'exercise_1',
                'title' => 'Exercise 1',
                'instructions' => 'Read the blog about chillies by a student called Bilal, and then answer the questions.',
                'stimulus' => $this->extractBetween(
                    $pastPaper->extracted_text,
                    "Bilal's blog about chillies",
                    "Question 1"
                ),
                'sort_order' => 1,
            ],
            [
                'section_key' => 'exercise_2',
                'title' => 'Exercise 2',
                'instructions' => 'Read the article about four young people (A-D) who have recently learned to drive. Then answer Question 7(a)-(i).',
                'stimulus' => $this->extractBetween(
                    $pastPaper->extracted_text,
                    'Learning to drive',
                    'For each statement, write the correct letter'
                ),
                'sort_order' => 2,
            ],
            [
                'section_key' => 'exercise_3',
                'title' => 'Exercise 3',
                'instructions' => 'Read the article about an actor called Alex Morgano, and then complete the notes.',
                'stimulus' => $this->extractBetween(
                    $pastPaper->extracted_text,
                    'Alex Morgano',
                    'Imagine you are going to give a talk'
                ),
                'sort_order' => 3,
            ],
            [
                'section_key' => 'exercise_4',
                'title' => 'Exercise 4',
                'instructions' => 'Read the article about an activity called cheerleading, and then answer the questions.',
                'stimulus' => $this->extractBetween(
                    $pastPaper->extracted_text,
                    'Cheerleading',
                    'For each question, choose the correct answer'
                ),
                'sort_order' => 4,
            ],
            [
                'section_key' => 'exercise_5',
                'title' => 'Exercise 5',
                'instructions' => null,
                'stimulus' => null,
                'sort_order' => 5,
            ],
            [
                'section_key' => 'exercise_6',
                'title' => 'Exercise 6',
                'instructions' => null,
                'stimulus' => null,
                'sort_order' => 6,
            ],
        ];

        foreach ($sections as $sectionData) {
            $section = QuestionSection::updateOrCreate(
                [
                    'past_paper_id' => $pastPaper->id,
                    'section_key' => $sectionData['section_key'],
                ],
                $sectionData
            );

            $this->info(
                "Section {$section->title}: " .
                ($section->stimulus ? 'stimulus found' : 'no separate stimulus')
            );
        }

        $questions = Question::where('past_paper_id', $pastPaper->id)
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            $this->warn('No questions were found for this past paper.');

            return self::SUCCESS;
        }

        $sectionMap = QuestionSection::where('past_paper_id', $pastPaper->id)
            ->get()
            ->keyBy('section_key');

        $linked = 0;

        foreach ($questions as $question) {
            $exercise = $question->metadata['section'] ?? null;

            if (! $exercise) {
                $this->warn("Question {$question->id} has no section metadata.");

                continue;
            }

            $sectionKey = strtolower(
                str_replace(' ', '_', trim($exercise))
            );

            $section = $sectionMap->get($sectionKey);

            if (! $section) {
                $this->warn(
                    "No section found for Question {$question->id}: {$exercise}"
                );

                continue;
            }

            $question->update([
                'section_id' => $section->id,
            ]);

            $linked++;
        }

        $this->newLine();

        $this->info("Sections created/updated: {$sectionMap->count()}");
        $this->info("Questions linked: {$linked}");

        $this->newLine();

        foreach ($sectionMap as $section) {
            $count = $section->questions()->count();

            $this->line(
                sprintf(
                    '%s: %d question(s)',
                    $section->title,
                    $count
                )
            );
        }

        $this->newLine();
        $this->info('SUCCESS: Past paper sections and questions are linked.');

        return self::SUCCESS;
    }

    private function extractBetween(
        string $text,
        string $start,
        string $end
    ): ?string {
        $startPosition = mb_stripos($text, $start);

        if ($startPosition === false) {
            return null;
        }

        $startPosition += mb_strlen($start);

        $endPosition = mb_stripos(
            $text,
            $end,
            $startPosition
        );

        if ($endPosition === false) {
            return trim(
                mb_substr(
                    $text,
                    $startPosition
                )
            );
        }

        return trim(
            mb_substr(
                $text,
                $startPosition,
                $endPosition - $startPosition
            )
        );
    }
}
<?php

namespace App\Console\Commands;

use App\Models\PastPaper;
use App\Services\MarkSchemeAnalyzer;
use App\Services\PastPaperTextExtractor;
use Illuminate\Console\Command;
use RuntimeException;

class AnalyzeMarkScheme extends Command
{
    protected $signature = 'questions:analyze-mark-scheme
                            {past_paper : Past paper ID}
                            {file : Path to the mark scheme PDF/DOC/DOCX}';

    protected $description =
        'Analyze an official mark scheme and attach marking rules to a past paper';

    public function handle(
        MarkSchemeAnalyzer $analyzer,
        PastPaperTextExtractor $extractor
    ): int {
        $pastPaper = PastPaper::find(
            $this->argument('past_paper')
        );

        if (! $pastPaper) {
            $this->error(
                'Past paper not found.'
            );

            return self::FAILURE;
        }

        $file = $this->argument('file');

        if (! is_file($file)) {
            $this->error(
                "Mark scheme file not found: {$file}"
            );

            return self::FAILURE;
        }

        $this->info(
            "Past paper: {$pastPaper->title}"
        );

        $this->info(
            "Reading mark scheme: {$file}"
        );

        try {
            /*
             * We reuse the existing PDF/DOC/DOCX extraction
             * infrastructure.
             */
            $text = $extractor->extractFile($file);

            if (trim($text) === '') {
                throw new RuntimeException(
                    'No text could be extracted from the mark scheme.'
                );
            }

            $this->info(
                'Extracted ' .
                number_format(
                    mb_strlen($text)
                ) .
                ' characters.'
            );

            $this->info(
                'Sending mark scheme to Gemini...'
            );

            $markScheme = $analyzer->analyze(
                $text
            );

            $questionCount = count(
                $markScheme['questions'] ?? []
            );

            $this->info(
                "Marking rules extracted: {$questionCount}"
            );

            $this->info(
                'Attaching rules to existing questions...'
            );

            $updated = $analyzer->attachToPastPaper(
                $pastPaper,
                $markScheme
            );

            $this->newLine();

            $this->info(
                "Questions updated: {$updated}"
            );

            $this->info(
                'Mark scheme successfully attached.'
            );

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error(
                'Mark scheme analysis failed.'
            );

            $this->error(
                $e->getMessage()
            );

            report($e);

            return self::FAILURE;
        }
    }
}
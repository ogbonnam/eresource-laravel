<?php

namespace App\Services;

use App\Models\PastPaper;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Spatie\PdfToText\Pdf;
use Throwable;

class PastPaperTextExtractor
{
    /**
     * Process a past paper and save the extracted text.
     */
    public function process(PastPaper $pastPaper): void
    {
        try {
            $pastPaper->update([
                'status' => 'processing',
                'processing_error' => null,
            ]);

            $filePath = storage_path(
                'app/public/' . ltrim($pastPaper->file_path, '/')
            );

            if (! is_file($filePath)) {
                throw new \RuntimeException(
                    "Past paper file was not found: {$filePath}"
                );
            }

            // Use the stored MIME type when available.
            // Otherwise detect it from the actual file.
            $mimeType = $pastPaper->mime_type;

            if (blank($mimeType)) {
                $mimeType = mime_content_type($filePath);
            }

            if (blank($mimeType)) {
                throw new \RuntimeException(
                    'Unable to determine the MIME type of the past paper.'
                );
            }

            $text = $this->extract(
                $filePath,
                $mimeType
            );

            if (blank($text)) {
                throw new \RuntimeException(
                    'No text could be extracted from the past paper.'
                );
            }

            $pastPaper->update([
                'mime_type' => $mimeType,
                'extracted_text' => $text,
                'status' => 'processed',
                'processing_error' => null,
                'processed_at' => now(),
            ]);
        } catch (Throwable $e) {
            $error = $this->cleanUtf8($e->getMessage());

            Log::error('Past paper processing failed', [
                'past_paper_id' => $pastPaper->id,
                'file' => $pastPaper->file_path,
                'error' => $error,
            ]);

            $pastPaper->update([
                'status' => 'failed',
                'processing_error' => $error,
            ]);

            throw $e;
        }
    }

    /**
     * Extract text from a past paper file.
     */
    public function extract(string $filePath, string $mimeType): string
    {
        try {
            $text = match ($mimeType) {
                'application/pdf' => $this->extractPdf($filePath),

                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    => $this->extractDocx($filePath),

                default => throw new \RuntimeException(
                    "Unsupported file type: {$mimeType}"
                ),
            };

            return $this->cleanUtf8($text);
        } catch (Throwable $e) {
            Log::error('Past paper text extraction failed', [
                'file' => $filePath,
                'mime_type' => $mimeType,
                'error' => $this->cleanUtf8($e->getMessage()),
            ]);

            throw $e;
        }
    }



    /**
     * Extract text from an arbitrary PDF/DOC/DOCX file.
     *
     * This reuses the same extraction logic used for past papers.
     */
    public function extractFile(string $filePath): string
    {
        if (! is_file($filePath)) {
            throw new \RuntimeException(
                "File was not found: {$filePath}"
            );
        }

        $mimeType = mime_content_type($filePath);

        if (blank($mimeType)) {
            throw new \RuntimeException(
                'Unable to determine the MIME type of the file.'
            );
        }

        return $this->extract(
            $filePath,
            $mimeType
        );
    }

    /**
     * Extract text from PDF.
     */
    protected function extractPdf(string $filePath): string
    {
        $binary = config('services.pdftotext.binary');

        if (! $binary || ! is_file($binary)) {
            throw new \RuntimeException(
                "pdftotext binary was not found at: {$binary}"
            );
        }

        $text = Pdf::getText(
            $filePath,
            $binary
        );

        return $this->cleanUtf8($text);
    }

    /**
     * Extract text from DOCX/DOC.
     */
    protected function extractDocx(string $filePath): string
    {
        $phpWord = IOFactory::load($filePath);

        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= $this->extractWordElement($element);
            }
        }

        return $this->cleanUtf8($text);
    }

    /**
     * Extract text from a PHPWord element.
     */
    protected function extractWordElement($element): string
    {
        $text = '';

        if (method_exists($element, 'getText')) {
            $value = $element->getText();

            if (is_string($value)) {
                $text .= $value . "\n";
            }
        }

        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $text .= $this->extractWordElement($child);
            }
        }

        return $text;
    }

    /**
     * Make extracted text safe for MySQL utf8mb4.
     */
    protected function cleanUtf8(string $text): string
    {
        // Remove UTF-8 BOM if present.
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text) ?? $text;

        /*
         * If the text is not valid UTF-8, treat the invalid bytes
         * as Windows-1252 and convert them to UTF-8.
         */
        if (! mb_check_encoding($text, 'UTF-8')) {
            $converted = @mb_convert_encoding(
                $text,
                'UTF-8',
                'Windows-1252'
            );

            if ($converted !== false) {
                $text = $converted;
            }
        }

        /*
         * Remove any remaining invalid UTF-8 bytes.
         */
        $cleaned = @iconv(
            'UTF-8',
            'UTF-8//IGNORE',
            $text
        );

        if ($cleaned !== false) {
            $text = $cleaned;
        }

        // Remove null bytes.
        $text = str_replace("\0", '', $text);

        return trim($text);
    }
}
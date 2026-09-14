<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('past_papers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('exam_type')
                ->nullable();

            $table->string('exam_year')
                ->nullable();

            $table->string('file_path');

            $table->string('file_name')
                ->nullable();

            $table->string('mime_type')
                ->nullable();

            $table->text('extracted_text')
                ->nullable();

            $table->enum('status', [
                'uploaded',
                'processing',
                'processed',
                'failed',
            ])->default('uploaded');

            $table->text('processing_error')
                ->nullable();

            $table->timestamp('processed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('past_papers');
    }
};
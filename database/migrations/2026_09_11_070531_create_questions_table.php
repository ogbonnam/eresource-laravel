<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            /*
             * Source information
             */
            $table->foreignId('past_paper_id')
                ->nullable()
                ->constrained('past_papers')
                ->nullOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            /*
             * Question classification
             */
            $table->string('topic')->nullable();
            $table->string('subtopic')->nullable();
            $table->string('concept')->nullable();

            $table->string('question_type')
                ->default('multiple_choice');

            $table->string('difficulty')
                ->default('medium');

            $table->string('command_word')->nullable();

            /*
             * Question content
             */
            $table->text('question');

            $table->unsignedInteger('marks')
                ->default(1);

            /*
             * Correct answer and explanation
             *
             * answer is kept as text so the same structure works
             * for MCQ, calculation, short answer and structured questions.
             */
            $table->text('answer')->nullable();

            $table->text('explanation')->nullable();

            /*
             * AI/source information
             */
            $table->enum('source_type', [
                'past_paper',
                'ai_generated',
                'manual',
            ])->default('ai_generated');

            $table->unsignedBigInteger('source_question_id')
                ->nullable();

            /*
             * Review / publishing workflow
             */
            $table->enum('status', [
                'draft',
                'pending_review',
                'approved',
                'rejected',
            ])->default('draft');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            /*
             * AI generation metadata
             */
            $table->string('ai_model')->nullable();

            $table->text('generation_notes')->nullable();

            /*
             * Extra metadata for future expansion.
             *
             * Examples:
             * - syllabus references
             * - learning objectives
             * - accepted alternative answers
             * - AI confidence
             */
            $table->json('metadata')->nullable();

            $table->timestamps();

            /*
             * Indexes
             */
            $table->index([
                'subject_id',
                'class_id',
            ]);

            $table->index([
                'topic',
                'subtopic',
            ]);

            $table->index('question_type');
            $table->index('difficulty');
            $table->index('status');
            $table->index('source_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
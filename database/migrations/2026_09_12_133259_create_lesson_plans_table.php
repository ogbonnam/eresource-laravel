<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();

            // The teacher who owns the lesson plan.
            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Faculty is stored directly so HOD/HOF vetting
            // can be restricted to their own faculty.
            $table->foreignId('faculty_id')
                ->constrained('faculties')
                ->cascadeOnDelete();

            // Existing course: Course -> Subject + Class + Teacher.
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->unsignedInteger('week')->nullable();

            $table->date('lesson_date')->nullable();

            $table->string('topic');

            $table->longText('objectives')->nullable();

            $table->longText('activities')->nullable();

            $table->longText('assessment')->nullable();

            // Optional uploaded lesson-plan document.
            $table->string('file_path')->nullable();

            // draft, pending, approved, rejected, revision_requested
            $table->string('status')
                ->default('draft')
                ->index();

            // Comments made by the teacher when submitting/resubmitting.
            $table->longText('teacher_comment')->nullable();

            // Comments from HOD/HOF/Admin during vetting.
            $table->longText('vetter_comment')->nullable();

            // User who approved/rejected/requested revision.
            $table->foreignId('vetted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('vetted_at')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // Useful indexes for filtering and dashboards.
            $table->index(['faculty_id', 'status']);
            $table->index(['teacher_id', 'status']);
            $table->index(['course_id', 'lesson_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_plans');
    }
};
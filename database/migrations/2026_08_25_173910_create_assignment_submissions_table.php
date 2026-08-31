<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();

            /*
             * Assignment being submitted.
             */
            $table->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();

            /*
             * Student who owns the submission.
             */
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Student's written answer.
             *
             * We use LONGTEXT so students can submit
             * substantial formatted content.
             */
            $table->longText('content')
                ->nullable();

            /*
             * Submission state.
             *
             * Possible values:
             *
             * draft
             * submitted
             * returned
             * graded
             */
            $table->string('status')
                ->default('draft');

            /*
             * When the student actually submitted it.
             */
            $table->timestamp('submitted_at')
                ->nullable();

            /*
             * Marks awarded by the teacher.
             */
            $table->unsignedInteger('grade')
                ->nullable();

            /*
             * Teacher's feedback.
             */
            $table->longText('feedback')
                ->nullable();

            /*
             * Teacher who graded the submission.
             */
            $table->foreignId('graded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * When the submission was graded.
             */
            $table->timestamp('graded_at')
                ->nullable();

            $table->timestamps();

            /*
             * A student should have only ONE current
             * submission record for an assignment.
             *
             * The student can update that submission
             * until the assignment rules prevent it.
             */
            $table->unique([
                'assignment_id',
                'student_id',
            ]);

            /*
             * Useful for teacher grading dashboards.
             */
            $table->index([
                'assignment_id',
                'status',
            ]);

            /*
             * Useful for finding a student's assignments.
             */
            $table->index([
                'student_id',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
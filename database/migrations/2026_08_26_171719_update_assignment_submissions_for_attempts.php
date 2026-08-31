<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {

            /*
             * ---------------------------------------------------------------
             * Attempt number
             * ---------------------------------------------------------------
             *
             * 1 = first submission
             * 2 = first resubmission
             * 3 = second resubmission
             * etc.
             */
            $table->unsignedTinyInteger('attempt_number')
                ->default(1)
                ->after('student_id');

            /*
             * ---------------------------------------------------------------
             * Late submission
             * ---------------------------------------------------------------
             *
             * Stores whether this particular attempt was submitted
             * after the normal due date.
             */
            $table->boolean('is_late')
                ->default(false)
                ->after('submitted_at');

            /*
             * The old constraint allowed only one submission
             * for each student/assignment.
             */
            $table->dropUnique(
                'assignment_submissions_assignment_id_student_id_unique'
            );

            /*
             * A student can now have multiple attempts,
             * but each attempt number must be unique.
             */
            $table->unique([
                'assignment_id',
                'student_id',
                'attempt_number',
            ], 'assignment_submissions_assignment_student_attempt_unique');

            /*
             * Useful for retrieving attempt history.
             */
            $table->index([
                'assignment_id',
                'student_id',
                'attempt_number',
            ], 'assignment_submissions_attempt_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {

            $table->dropUnique(
                'assignment_submissions_assignment_student_attempt_unique'
            );

            $table->dropIndex(
                'assignment_submissions_attempt_lookup_index'
            );

            $table->unique([
                'assignment_id',
                'student_id',
            ]);

            $table->dropColumn([
                'attempt_number',
                'is_late',
            ]);
        });
    }
};
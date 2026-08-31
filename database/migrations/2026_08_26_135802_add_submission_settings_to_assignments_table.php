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
        Schema::table('assignments', function (Blueprint $table) {

            /*
             * ---------------------------------------------------------------
             * Late submissions
             * ---------------------------------------------------------------
             *
             * Determines whether students may submit after the
             * normal due date.
             */
            $table->boolean('allow_late_submission')
                ->default(false)
                ->after('due_at');


            /*
             * ---------------------------------------------------------------
             * Late submission deadline
             * ---------------------------------------------------------------
             *
             * Optional final deadline for late submissions.
             *
             * Example:
             *
             * Due date:
             * 28 Aug 2026 11:59 PM
             *
             * Late submission until:
             * 31 Aug 2026 11:59 PM
             *
             * NULL means there is no separate late deadline.
             */
            $table->timestamp('late_submission_until')
                ->nullable()
                ->after('allow_late_submission');


            /*
             * ---------------------------------------------------------------
             * Resubmissions
             * ---------------------------------------------------------------
             *
             * Determines whether a student can submit another attempt
             * after already submitting an assignment.
             */
            $table->boolean('allow_resubmission')
                ->default(false)
                ->after('late_submission_until');


            /*
             * ---------------------------------------------------------------
             * Maximum attempts
             * ---------------------------------------------------------------
             *
             * Total number of submissions a student is allowed to make.
             *
             * 1 = only one submission
             * 2 = original + one resubmission
             * 3 = original + two resubmissions
             */
            $table->unsignedTinyInteger('max_attempts')
                ->default(1)
                ->after('allow_resubmission');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {

            $table->dropColumn([
                'allow_late_submission',
                'late_submission_until',
                'allow_resubmission',
                'max_attempts',
            ]);
        });
    }
};
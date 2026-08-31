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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            /*
             * Course this assignment belongs to.
             */
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            /*
             * Teacher who created the assignment.
             */
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Assignment information.
             */
            $table->string('title');

            $table->text('description')
                ->nullable();

            /*
             * Full assignment instructions.
             *
             * This can contain formatted HTML from
             * the editor, similar to Resources.
             */
            $table->longText('instructions')
                ->nullable();

            /*
             * Maximum marks available.
             */
            $table->unsignedInteger('total_marks')
                ->default(100);

            /*
             * Assignment deadline.
             */
            $table->timestamp('due_at')
                ->nullable();

            /*
             * Controls whether students can see
             * the assignment.
             */
            $table->boolean('is_published')
                ->default(false);

            /*
             * When the assignment was published.
             */
            $table->timestamp('published_at')
                ->nullable();

            $table->timestamps();

            /*
             * Common queries:
             *
             * - assignments for a course
             * - published assignments
             * - assignments ordered by deadline
             */
            $table->index([
                'course_id',
                'is_published',
                'due_at',
            ]);

            /*
             * Useful when retrieving assignments
             * created by a particular teacher.
             */
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
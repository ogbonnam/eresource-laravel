<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_plans', function (Blueprint $table) {
            $table->foreignId('class_id')
                ->nullable()
                ->after('faculty_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->nullable()
                ->after('class_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');

            $table->index(['class_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::table('lesson_plans', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->nullable()
                ->after('faculty_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->dropForeign(['subject_id']);
            $table->dropForeign(['class_id']);

            $table->dropIndex(['class_id', 'subject_id']);

            $table->dropColumn(['class_id', 'subject_id']);
        });
    }
};
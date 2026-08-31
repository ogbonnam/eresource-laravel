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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('code')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->string('thumbnail')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /*
             * A class should not have the same subject twice.
             *
             * Example:
             *
             * JSS 1A + Mathematics = one course
             */
            $table->unique(
                ['class_id', 'subject_id'],
                'courses_class_subject_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();

            /*
             * The course this resource belongs to.
             */
            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            /*
             * Resource information.
             */
            $table->string('title');

            $table->text('description')
                ->nullable();

            /*
             * Examples:
             *
             * pdf
             * document
             * presentation
             * video
             * image
             * link
             * file
             */
            $table->string('type');

            /*
             * Used for uploaded resources.
             *
             * Example:
             * resources/biology/cell-structure.pdf
             */
            $table->string('file_path')
                ->nullable();

            /*
             * Used for external resources.
             *
             * Example:
             * https://www.youtube.com/watch?v=...
             */
            $table->text('url')
                ->nullable();

            /*
             * Optional thumbnail/cover image.
             */
            $table->string('thumbnail')
                ->nullable();

            /*
             * Controls whether students can currently see
             * the resource.
             */
            $table->boolean('is_published')
                ->default(false);

            /*
             * Controls ordering inside a course.
             */
            $table->unsignedInteger('sort_order')
                ->default(0);

            /*
             * Optional publication date.
             *
             * This allows us later to prepare resources
             * before making them visible.
             */
            $table->timestamp('published_at')
                ->nullable();

            $table->timestamps();

            /*
             * Speeds up common course/resource queries.
             */
            $table->index([
                'course_id',
                'is_published',
                'sort_order',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
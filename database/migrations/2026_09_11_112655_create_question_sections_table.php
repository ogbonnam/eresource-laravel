<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('past_paper_id')
                ->constrained('past_papers')
                ->cascadeOnDelete();

            $table->string('section_key');
            $table->string('title')->nullable();

            $table->text('instructions')->nullable();

            $table->longText('stimulus')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['past_paper_id', 'sort_order']);
            $table->index(['past_paper_id', 'section_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_sections');
    }
};
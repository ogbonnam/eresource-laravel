<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mark_schemes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('past_paper_id')
                ->constrained('past_papers')
                ->cascadeOnDelete();

            $table->string('file_path');

            $table->string('file_name');

            $table->string('mime_type')->nullable();

            $table->longText('extracted_text')->nullable();

            $table->enum('status', [
                'uploaded',
                'processing',
                'processed',
                'analyzing',
                'analyzed',
                'failed',
            ])->default('uploaded');

            $table->text('processing_error')->nullable();

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->index([
                'past_paper_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mark_schemes');
    }
};
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
        Schema::create('assignment_submission_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assignment_submission_id')
                ->constrained('assignment_submissions')
                ->cascadeOnDelete();

            /*
             * Original filename uploaded by the student.
             */
            $table->string('original_name');

            /*
             * Actual stored filename/path.
             */
            $table->string('file_path');

            /*
             * MIME type.
             */
            $table->string('mime_type')->nullable();

            /*
             * File size in bytes.
             */
            $table->unsignedBigInteger('file_size')->nullable();

            $table->timestamps();

            $table->index('assignment_submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submission_files');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('section_id')
                ->nullable()
                ->after('past_paper_id')
                ->constrained('question_sections')
                ->nullOnDelete();

            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropIndex(['section_id']);
            $table->dropColumn('section_id');
        });
    }
};
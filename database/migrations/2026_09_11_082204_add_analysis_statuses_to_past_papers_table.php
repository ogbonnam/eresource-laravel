<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('past_papers', function (Blueprint $table) {
            $table->enum('status', [
                'uploaded',
                'processing',
                'processed',
                'analyzing',
                'analyzed',
                'failed',
            ])->default('uploaded')->change();
        });
    }

    public function down(): void
    {
        Schema::table('past_papers', function (Blueprint $table) {
            $table->enum('status', [
                'uploaded',
                'processing',
                'processed',
                'failed',
            ])->default('uploaded')->change();
        });
    }
};
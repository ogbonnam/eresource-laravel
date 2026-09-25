<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_views', function (Blueprint $table) {
            $table->id();

            $table->foreignId('broadcast_id')
                ->constrained('broadcasts')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('viewed_at');

            $table->timestamps();

            $table->index([
                'broadcast_id',
                'teacher_id',
            ]);

            $table->index([
                'broadcast_id',
                'viewed_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_views');
    }
};
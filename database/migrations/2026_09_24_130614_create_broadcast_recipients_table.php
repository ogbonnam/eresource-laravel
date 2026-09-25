<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_recipients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('broadcast_id')
                ->constrained('broadcasts')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamp('first_opened_at')
                ->nullable();

            $table->timestamp('last_opened_at')
                ->nullable();

            $table->timestamp('read_at')
                ->nullable();

            $table->unsignedInteger('open_count')
                ->default(0);

            $table->timestamp('acknowledged_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'broadcast_id',
                'teacher_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_recipients');
    }
};
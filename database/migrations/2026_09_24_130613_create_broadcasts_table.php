<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');

            $table->longText('message');

            $table->enum('target_type', [
                'all',
                'department',
                'individual',
            ]);

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('faculties')
                ->nullOnDelete();

            $table->text('google_sheet_url')
                ->nullable();

            $table->timestamp('sent_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
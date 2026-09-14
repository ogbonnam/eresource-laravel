<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('faculty_id')
                ->nullable()
                ->after('role')
                ->constrained('faculties')
                ->nullOnDelete();

            $table->string('staff_position')
                ->default('teacher')
                ->after('faculty_id');

            $table->index('staff_position');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropColumn([
                'faculty_id',
                'staff_position',
            ]);
        });
    }
};
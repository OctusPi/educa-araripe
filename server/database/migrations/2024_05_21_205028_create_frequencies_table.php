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
        Schema::create('frequencies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('organ')->constrained('organs');
            $table->foreignId('school')->constrained('schools');
            $table->foreignId('serie')->constrained('series');
            $table->foreignId('classe')->constrained('classes');
            $table->foreignId('subject')->constrained('subjects');
            $table->foreignId('teacher')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('student')->constrained('students');
            $table->date('date_miss');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequencies');
    }
};

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
        Schema::create('word_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id_1')->constrained('words')->onDelete('cascade');
            $table->foreignId('word_id_2')->constrained('words')->onDelete('cascade');
            $table->string('type'); // 'synonyms', 'antonyms'
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['word_id_1', 'word_id_2', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('word_connections');
    }
};

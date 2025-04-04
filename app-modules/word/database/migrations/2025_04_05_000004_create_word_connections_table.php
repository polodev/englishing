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
            $table->foreignId('word_id_1');
            $table->foreignId('word_id_2');
            $table->string('type'); // 'synonyms', 'antonyms'
            $table->timestamps();

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

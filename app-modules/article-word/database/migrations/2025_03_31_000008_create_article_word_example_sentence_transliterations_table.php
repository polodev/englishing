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
        Schema::create('article_word_example_sentence_transliterations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_example_sentence_translation_id')->constrained()->onDelete('cascade');
            $table->text('bn_transliteration')->nullable();
            $table->text('hi_transliteration')->nullable();
            $table->text('es_transliteration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_word_example_sentence_transliterations');
    }
};

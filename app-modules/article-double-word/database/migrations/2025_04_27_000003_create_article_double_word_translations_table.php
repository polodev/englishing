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
        Schema::create('article_double_word_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_double_word_set_list_id');
            $table->string('word_1_translation')->nullable();
            $table->string('word_2_translation')->nullable();
            $table->string('word_1_transliteration')->nullable();
            $table->string('word_2_transliteration')->nullable();
            $table->string('locale');
            $table->string('slug')->nullable();
            $table->timestamps();
            
            // Create a unique constraint for one translation per locale per double word set list
            $table->unique(['article_double_word_set_list_id', 'locale'], 'unique_double_word_trans_locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_double_word_translations');
    }
};

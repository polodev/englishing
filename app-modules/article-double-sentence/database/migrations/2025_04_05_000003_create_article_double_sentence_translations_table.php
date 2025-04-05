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
        Schema::create('article_double_sentence_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_double_sentence_set_list_id');
            $table->text('sentence_1_translation');
            $table->text('sentence_2_translation');
            $table->text('sentence_1_transliteration')->nullable();
            $table->text('sentence_2_transliteration')->nullable();
            $table->string('locale', 10);
            $table->timestamps();
            
            $table->unique(['article_double_sentence_set_list_id', 'locale'], 'article_double_sentence_translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_double_sentence_translations');
    }
};

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
        Schema::create('article_expression_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_expression_set_list_id');
            $table->text('expression_translation');
            $table->text('expression_transliteration')->nullable();
            $table->text('example_sentence_translation')->nullable();
            $table->text('example_sentence_transliteration')->nullable();
            $table->string('locale', 10);
            $table->string('source')->nullable();
            $table->timestamps();

            $table->unique(['article_expression_set_list_id', 'locale'], 'article_expression_translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_expression_translations');
    }
};

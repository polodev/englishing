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
        Schema::create('article_word_example_sentence_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_example_sentence_id');
            $table->text('bn_sentence')->nullable();
            $table->text('hi_sentence')->nullable();
            $table->text('es_sentence')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_word_example_sentence_translations');
    }
};

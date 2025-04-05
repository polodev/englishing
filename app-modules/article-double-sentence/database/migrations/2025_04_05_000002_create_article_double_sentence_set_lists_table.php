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
        Schema::create('article_double_sentence_set_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_double_sentence_set_id');
            $table->text('sentence_1');
            $table->string('sentence_1_slug');
            $table->text('sentence_2');
            $table->string('sentence_2_slug');
            $table->integer('display_order')->default(0);
            $table->json('pronunciation_1')->nullable();
            $table->json('pronunciation_2')->nullable();
            $table->timestamps();
            
            $table->unique(['article_double_sentence_set_id', 'sentence_1_slug', 'sentence_2_slug'], 'article_double_sentence_set_lists_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_double_sentence_set_lists');
    }
};

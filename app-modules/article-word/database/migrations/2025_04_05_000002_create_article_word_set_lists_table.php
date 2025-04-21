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
        Schema::create('article_word_set_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_set_id');
            $table->integer('display_order')->default(0);
            $table->string('word');
            $table->string('slug');
            $table->string('phonetic')->nullable();
            $table->json('pronunciation')->nullable();
            $table->string('parts_of_speech')->nullable();
            $table->text('static_content_1')->nullable();
            $table->text('static_content_2')->nullable();
            $table->text('meaning')->nullable();
            $table->text('example_sentence')->nullable();
            $table->text('example_expression')->nullable();
            $table->text('example_expression_meaning')->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();

            $table->unique(['article_word_set_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_word_set_lists');
    }
};

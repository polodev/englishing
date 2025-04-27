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
        Schema::create('article_triple_word_set_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_triple_word_set_id');
            $table->string('word_1');
            $table->string('word_1_slug')->index();
            $table->string('word_2');
            $table->string('word_2_slug')->index();
            $table->string('word_3');
            $table->string('word_3_slug')->index();
            $table->text('word_1_meaning')->nullable();
            $table->text('word_2_meaning')->nullable();
            $table->text('word_3_meaning')->nullable();
            $table->text('word_1_example_sentence')->nullable();
            $table->text('word_2_example_sentence')->nullable();
            $table->text('word_3_example_sentence')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Create a unique constraint on the set_id and all three slugs together
            $table->unique(['article_triple_word_set_id', 'word_1_slug', 'word_2_slug', 'word_3_slug'], 'unique_triple_word_slugs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_triple_word_set_lists');
    }
};

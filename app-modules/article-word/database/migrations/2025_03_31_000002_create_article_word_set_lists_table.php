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
            $table->integer('display_order')->nullable()->default(0);
            $table->string('word');
            $table->string('slug');
            $table->string('phonetic')->nullable();
            $table->string('part_of_speech')->nullable();
            $table->text('static_content_1')->nullable();
            $table->text('static_content_2')->nullable();
            $table->string('parts_of_speech')->nullable();
            $table->timestamps();
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

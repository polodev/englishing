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
        Schema::create('article_word_example_sentences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_set_list_id')->constrained()->onDelete('cascade');
            $table->integer('display_order')->nullable()->default(0);
            $table->text('sentence');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_word_example_sentences');
    }
};

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
            $table->foreignId('article_double_sentence_set_list_id')->constrained()->onDelete('cascade');
            $table->text('sentence_1_bn_meaning')->nullable();
            $table->text('sentence_1_hi_meaning')->nullable();
            $table->text('sentence_1_es_meaning')->nullable();
            $table->text('sentence_2_bn_meaning')->nullable();
            $table->text('sentence_2_hi_meaning')->nullable();
            $table->text('sentence_2_es_meaning')->nullable();
            $table->timestamps();
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

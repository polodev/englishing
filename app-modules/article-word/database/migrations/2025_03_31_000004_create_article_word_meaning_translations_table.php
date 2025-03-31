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
        Schema::create('article_word_meaning_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_meaning_id')->constrained()->onDelete('cascade');
            $table->text('bn_meaning')->nullable();
            $table->text('hi_meaning')->nullable();
            $table->text('es_meaning')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_word_meaning_translations');
    }
};

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
        Schema::create('article_word_set_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_word_set_id')->constrained()->onDelete('cascade');
            $table->string('bn_title')->nullable();
            $table->string('hi_title')->nullable();
            $table->string('es_title')->nullable();
            $table->text('bn_content')->nullable();
            $table->text('hi_content')->nullable();
            $table->text('es_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_word_set_translations');
    }
};

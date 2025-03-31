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
        Schema::create('article_sentence_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_sentence_set_list_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('article_sentence_translations');
    }
};

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
        Schema::create('article_double_sentence_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id');
            $table->integer('display_order')->default(0);
            $table->string('title');
            $table->text('content')->nullable();
            $table->json('title_translation')->nullable();
            $table->json('content_translation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_double_sentence_sets');
    }
};

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
        Schema::create('article_conversation_message_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_conversation_message_id');
            $table->text('translation');
            $table->text('transliteration')->nullable();
            $table->string('locale', 10);
            $table->timestamps();
            
            $table->unique(['article_conversation_message_id', 'locale'], 'article_conversation_message_translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_conversation_message_translations');
    }
};

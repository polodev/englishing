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
        Schema::create('article_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->integer('display_order')->default(0);
            $table->string('title');
            $table->string('slug');
            $table->text('content')->nullable();
            $table->json('title_translation')->nullable();
            $table->json('content_translation')->nullable();
            $table->timestamps();

            $table->unique(['article_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_conversations');
    }
};

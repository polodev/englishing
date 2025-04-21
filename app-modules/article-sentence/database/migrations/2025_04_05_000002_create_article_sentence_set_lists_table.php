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
        Schema::create('article_sentence_set_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_sentence_set_id');
            $table->text('sentence');
            $table->string('slug');
            $table->integer('display_order')->default(0);
            $table->json('pronunciation')->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();

            $table->unique(['article_sentence_set_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_sentence_set_lists');
    }
};

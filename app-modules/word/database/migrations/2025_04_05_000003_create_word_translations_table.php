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
        Schema::create('word_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id');
            $table->foreignId('meaning_id')->nullable();
            $table->text('translation');
            $table->text('transliteration')->nullable();
            $table->string('locale', 10);
            $table->string('source')->nullable();
            $table->timestamps();

            $table->unique(['word_id', 'locale', 'meaning_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('word_translations');
    }
};

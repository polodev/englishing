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
        Schema::create('sentence_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sentence_id');
            $table->text('translation');
            $table->text('transliteration')->nullable();
            $table->string('slug');
            $table->string('locale', 10);
            $table->string('source')->nullable();
            $table->timestamps();

            $table->unique(['sentence_id', 'locale', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentence_translations');
    }
};

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
        Schema::create('expression_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expression_id');
            $table->foreignId('expression_meaning_id')->nullable();
            $table->text('translation');
            $table->text('transliteration')->nullable();
            $table->string('slug');
            $table->string('locale', 10);
            $table->string('source')->nullable();
            $table->timestamps();
            
            $table->unique(['expression_id', 'locale', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expression_translations');
    }
};

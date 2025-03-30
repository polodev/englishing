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
        Schema::create('word_pronunciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id')->constrained()->onDelete('cascade');
            $table->string('bn_pronunciation')->nullable();
            $table->string('hi_pronunciation')->nullable();
            $table->string('es_pronunciation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('word_pronunciations');
    }
};

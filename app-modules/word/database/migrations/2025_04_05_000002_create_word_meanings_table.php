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
        Schema::create('word_meanings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id');
            $table->text('meaning');
            $table->string('slug');
            $table->string('source')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->unique(['word_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('word_meanings');
    }
};

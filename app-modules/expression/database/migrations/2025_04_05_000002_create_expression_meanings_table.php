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
        Schema::create('expression_meanings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expression_id');
            $table->text('meaning');
            $table->string('source')->nullable();
            $table->string('slug');
            $table->json('pronunciation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expression_meanings');
    }
};

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
        Schema::create('expression_pronunciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expression_id');
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
        Schema::dropIfExists('expression_pronunciations');
    }
};

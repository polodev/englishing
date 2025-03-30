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
        Schema::create('expression_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expression_id_1')->constrained('expressions')->onDelete('cascade');
            $table->foreignId('expression_id_2')->constrained('expressions')->onDelete('cascade');
            $table->string('type'); // 'synonyms', 'antonyms'
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['expression_id_1', 'expression_id_2', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expression_connections');
    }
};

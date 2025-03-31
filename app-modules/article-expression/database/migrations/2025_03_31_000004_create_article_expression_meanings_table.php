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
        Schema::create('article_expression_meanings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_expression_set_list_id')->constrained()->onDelete('cascade');
            $table->text('meaning');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_expression_meanings');
    }
};

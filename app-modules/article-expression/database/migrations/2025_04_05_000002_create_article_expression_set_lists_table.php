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
        Schema::create('article_expression_set_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_expression_set_id');
            $table->text('expression');
            $table->string('type');
            $table->string('slug');
            $table->integer('display_order')->default(0);
            $table->text('meaning')->nullable();
            $table->text('example_sentence')->nullable();
            $table->json('pronunciation')->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();

            $table->unique(['article_expression_set_id', 'slug'], 'article_expression_set_list_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_expression_set_lists');
    }
};

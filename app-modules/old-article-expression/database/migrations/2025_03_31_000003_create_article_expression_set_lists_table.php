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
            $table->string('slug');
            $table->integer('display_order')->nullable()->default(0);
            $table->text('static_content_1')->nullable();
            $table->text('static_content_2')->nullable();
            $table->timestamps();
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

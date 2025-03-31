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
        Schema::create('article_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->integer('display_order')->nullable()->default(0);
            $table->boolean('is_premium')->default(false);
            $table->text('scratchpad')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_conversations');
    }
};

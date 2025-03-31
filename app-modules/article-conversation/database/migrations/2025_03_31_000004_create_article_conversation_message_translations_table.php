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
        Schema::create('article_conversation_message_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_conversation_message_id')->constrained()->onDelete('cascade');
            $table->text('bn_message')->nullable();
            $table->text('hi_message')->nullable();
            $table->text('es_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_conversation_message_translations');
    }
};

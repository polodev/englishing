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
        Schema::create('article_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_conversation_id');
            $table->string('speaker'); // speaker_1, speaker_2, speaker_3
            $table->text('message');
            $table->string('slug');
            $table->integer('display_order')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_conversation_messages');
    }
};

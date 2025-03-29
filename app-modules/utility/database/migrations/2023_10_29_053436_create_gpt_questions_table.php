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
        Schema::create('gpt_questions', function (Blueprint $table) {
            $table->id();
            
            $table->string('title');

            $table->foreignId('user_id') ->nullable();

            $table->text('content');
            $table->string('rc_1')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gpt_questions');
    }
};

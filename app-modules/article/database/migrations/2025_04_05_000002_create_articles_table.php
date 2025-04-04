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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('course_id')->nullable();
            $table->string('type')->nullable(); // tutorial, guide, reference, etc.
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->integer('display_order')->default(0);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->text('excerpt')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->text('scratchpad')->nullable();
            $table->json('title_translation')->nullable();
            $table->json('content_translation')->nullable();
            $table->json('excerpt_translation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

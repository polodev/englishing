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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('series_id');
            $table->string('title');
            $table->string('slug');
            $table->text('content')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Create a unique index on series_id and slug
            $table->unique(['series_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};

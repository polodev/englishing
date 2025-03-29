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
        Schema::create('seed_trackers', function (Blueprint $table) {
            $table->id();
            
            $table->morphs('seedtrackable');
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->unique([
                'seedtrackable_id',
                'seedtrackable_type',
            ], 'seedtracker_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seed_trackers');
    }
};

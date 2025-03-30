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
        Schema::create('title_content_remark_locales', function (Blueprint $table) {
            $table->id();
            $table->morphs('tcrlable');
            $table->string('locale');
            $table->string('title')->nullable();
            $table->longText('remarks')->nullable();
            $table->longText('content')->nullable();
            $table->string('rc_1')->nullable();
            $table->text('title_transliteration')->nullable();
            

            $table->timestamps();
            $table->unique([
              'tcrlable_type',
              'tcrlable_id',
              'locale',
            ], 'tcrlu');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_content_remark_locales');
    }
};

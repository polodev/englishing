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
        
        Schema::create('column_title_locales', function (Blueprint $table) {
            $table->id();
            $table->morphs('ctlocaleable');
            $table->string('locale');

            $table->string('column_title_1')->nullable()->comment('Column title 1');
            $table->string('column_title_2')->nullable()->comment('Column title 2');
            $table->string('column_title_3')->nullable()->comment('Column title 3');
            $table->string('column_title_4')->nullable()->comment('Column title 4');
            $table->string('static_text_column_title')->nullable()->comment('static_text Column title');
            $table->string('wrong_text_column_title')->nullable();
            $table->string('remarks_column_title')->nullable()->comment('Remarks Column title');

            $table->string('rc_1')->nullable();
            
            
            $table->softDeletes();
            $table->unique([
              'ctlocaleable_type',
              'ctlocaleable_id',
              'locale',
            ], 'c_t_l_unique');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('column_title_locales');
    }
};

<?php

namespace Modules\Expression\Database\Seeders;

use Illuminate\Database\Seeder;

class ExpressionModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            ExpressionSeeder::class,
        ]);
    }
}

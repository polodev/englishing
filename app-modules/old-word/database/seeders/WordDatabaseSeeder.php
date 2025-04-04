<?php

namespace Modules\Word\Database\Seeders;

use Illuminate\Database\Seeder;

class WordDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            WordSeeder::class,
        ]);
    }
}

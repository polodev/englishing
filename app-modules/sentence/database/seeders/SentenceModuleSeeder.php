<?php

namespace Modules\Sentence\Database\Seeders;

use Illuminate\Database\Seeder;

class SentenceModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SentenceSeeder::class,
        ]);
    }
}

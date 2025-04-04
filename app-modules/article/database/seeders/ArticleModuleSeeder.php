<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;

class ArticleModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CourseSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}

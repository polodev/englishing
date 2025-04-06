<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Article\Database\Seeders\ArticleSeeder;
use Modules\Article\Database\Seeders\CourseSeeder;
use Modules\Expression\Database\Seeders\ExpressionSeeder;
use Modules\Sentence\Database\Seeders\SentenceSeeder;
use Modules\Word\Database\Seeders\WordSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            WordSeeder::class,
            SentenceSeeder::class,
            ExpressionSeeder::class,
            CourseSeeder::class,
            ArticleSeeder::class,
        ]);


    }
}

<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Article\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'user_id' => 1,
                'title' => 'English Grammar Basics',
                'content' => 'Learn the fundamentals of English grammar from scratch.',
                'slug' => 'english-grammar-basics',
                'title_translation' => [
                    'bn' => 'ইংরেজি ব্যাকরণের মৌলিক বিষয়',
                    'hi' => 'अंग्रेजी व्याकरण की मूल बातें',
                ],
                'content_translation' => [
                    'bn' => 'শূন্য থেকে ইংরেজি ব্যাকরণের মৌলিক বিষয়গুলি শিখুন।',
                    'hi' => 'शुरुआत से अंग्रेजी व्याकरण की मूल बातें सीखें।',
                ],
                'status' => 'published',
            ],
            [
                'user_id' => 1,
                'title' => 'Common English Idioms',
                'content' => 'A comprehensive guide to common English idioms and phrases.',
                'slug' => 'common-english-idioms',
                'title_translation' => [
                    'bn' => 'সাধারণ ইংরেজি বাগধারা',
                    'hi' => 'आम अंग्रेजी मुहावरे',
                ],
                'content_translation' => [
                    'bn' => 'সাধারণ ইংরেজি বাগধারা এবং বাক্যাংশগুলির একটি বিস্তৃত গাইড।',
                    'hi' => 'आम अंग्रेजी मुहावरों और वाक्यांशों के लिए एक व्यापक गाइड।',
                ],
                'status' => 'published',
            ],
            [
                'user_id' => 1,
                'title' => 'Business English',
                'content' => 'Learn professional English for business communication.',
                'slug' => 'business-english',
                'title_translation' => [
                    'bn' => 'ব্যবসায়িক ইংরেজি',
                    'hi' => 'व्यापारिक अंग्रेजी',
                ],
                'content_translation' => [
                    'bn' => 'ব্যবসায়িক যোগাযোগের জন্য পেশাদার ইংরেজি শিখুন।',
                    'hi' => 'व्यावसायिक संचार के लिए पेशेवर अंग्रेजी सीखें।',
                ],
                'status' => 'draft',
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }
    }
}

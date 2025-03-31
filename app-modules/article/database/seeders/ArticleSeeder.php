<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Article\Models\Article;
use Modules\Article\Models\ArticleTranslation;
use Modules\Article\Models\Section;
use Modules\Article\Models\SectionTranslation;
use Modules\Article\Models\Series;
use Modules\Article\Models\SeriesTranslation;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user (or create one if none exists)
        $user = User::first() ?? User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create a sample series
        $englishGrammarSeries = Series::create([
            'user_id' => $user->id,
            'title' => 'English Grammar Fundamentals',
            'slug' => 'english-grammar-fundamentals',
            'content' => 'A comprehensive guide to English grammar for beginners and intermediate learners.',
        ]);

        // Create series translation
        SeriesTranslation::create([
            'series_id' => $englishGrammarSeries->id,
            'bn_title' => 'ইংরেজি ব্যাকরণের মৌলিক বিষয়',
            'hi_title' => 'अंग्रेजी व्याकरण की मूल बातें',
            'es_title' => 'Fundamentos de gramática inglesa',
            'bn_content' => 'নতুন এবং মাঝারি শিক্ষার্থীদের জন্য ইংরেজি ব্যাকরণের একটি বিস্তৃত গাইড।',
            'hi_content' => 'शुरुआती और मध्यवर्ती सीखने वालों के लिए अंग्रेजी व्याकरण का एक व्यापक मार्गदर्शक।',
            'es_content' => 'Una guía completa de gramática inglesa para estudiantes principiantes e intermedios.',
        ]);

        // Create sections
        $sections = [
            [
                'title' => 'Parts of Speech',
                'slug' => 'parts-of-speech',
                'content' => 'Understanding the different parts of speech is essential for mastering English grammar.',
                'display_order' => 1,
                'translation' => [
                    'bn_title' => 'বাক্যের অংশ',
                    'hi_title' => 'वाक्य के भाग',
                    'es_title' => 'Partes del discurso',
                    'bn_content' => 'ইংরেজি ব্যাকরণ আয়ত্ত করার জন্য বাক্যের বিভিন্ন অংশ বোঝা অপরিহার্য।',
                    'hi_content' => 'अंग्रेजी व्याकरण में महारत हासिल करने के लिए वाक्य के विभिन्न भागों को समझना आवश्यक है।',
                    'es_content' => 'Comprender las diferentes partes del discurso es esencial para dominar la gramática inglesa.',
                ],
                'articles' => [
                    [
                        'type' => 'lesson',
                        'title' => 'Nouns: Definition and Types',
                        'slug' => 'nouns-definition-and-types',
                        'content' => 'Nouns are words that name people, places, things, or ideas. There are several types of nouns including common nouns, proper nouns, abstract nouns, and collective nouns.',
                        'display_order' => 1,
                        'excerpt' => 'Learn about nouns and their different types in English grammar.',
                        'is_premium' => false,
                        'translation' => [
                            'bn_title' => 'বিশেষ্য: সংজ্ঞা এবং প্রকারভেদ',
                            'hi_title' => 'संज्ञा: परिभाषा और प्रकार',
                            'es_title' => 'Sustantivos: Definición y tipos',
                            'bn_content' => 'বিশেষ্য হল এমন শব্দ যা মানুষ, স্থান, জিনিস বা ধারণার নাম দেয়। বিশেষ্যের বেশ কয়েকটি প্রকার রয়েছে যার মধ্যে রয়েছে সাধারণ বিশেষ্য, নির্দিষ্ট বিশেষ্য, বিমূর্ত বিশেষ্য এবং সমষ্টিবাচক বিশেষ্য।',
                            'hi_content' => 'संज्ञा वे शब्द हैं जो लोगों, स्थानों, वस्तुओं या विचारों का नाम देते हैं। संज्ञा के कई प्रकार हैं जिनमें सामान्य संज्ञा, व्यक्तिवाचक संज्ञा, अमूर्त संज्ञा और समूहवाचक संज्ञा शामिल हैं।',
                            'es_content' => 'Los sustantivos son palabras que nombran personas, lugares, cosas o ideas. Hay varios tipos de sustantivos, incluidos sustantivos comunes, sustantivos propios, sustantivos abstractos y sustantivos colectivos.',
                            'bn_excerpt' => 'ইংরেজি ব্যাকরণে বিশেষ্য এবং তাদের বিভিন্ন প্রকার সম্পর্কে জানুন।',
                            'hi_excerpt' => 'अंग्रेजी व्याकरण में संज्ञा और उनके विभिन्न प्रकारों के बारे में जानें।',
                            'es_excerpt' => 'Aprenda sobre los sustantivos y sus diferentes tipos en la gramática inglesa.',
                        ],
                    ],
                    [
                        'type' => 'lesson',
                        'title' => 'Verbs: Definition and Types',
                        'slug' => 'verbs-definition-and-types',
                        'content' => 'Verbs are words that express action, occurrence, or state of being. Types of verbs include action verbs, linking verbs, helping verbs, regular verbs, and irregular verbs.',
                        'display_order' => 2,
                        'excerpt' => 'Explore verbs and their different types in English grammar.',
                        'is_premium' => false,
                        'translation' => [
                            'bn_title' => 'ক্রিয়া: সংজ্ঞা এবং প্রকারভেদ',
                            'hi_title' => 'क्रिया: परिभाषा और प्रकार',
                            'es_title' => 'Verbos: Definición y tipos',
                            'bn_content' => 'ক্রিয়া হল এমন শব্দ যা কাজ, ঘটনা বা অস্তিত্বের অবস্থা প্রকাশ করে। ক্রিয়ার প্রকারগুলির মধ্যে রয়েছে কর্ম ক্রিয়া, সংযোগকারী ক্রিয়া, সহায়ক ক্রিয়া, নিয়মিত ক্রিয়া এবং অনিয়মিত ক্রিয়া।',
                            'hi_content' => 'क्रिया वे शब्द हैं जो क्रिया, घटना या होने की स्थिति व्यक्त करते हैं। क्रिया के प्रकारों में क्रिया क्रिया, लिंकिंग क्रिया, सहायक क्रिया, नियमित क्रिया और अनियमित क्रिया शामिल हैं।',
                            'es_content' => 'Los verbos son palabras que expresan acción, ocurrencia o estado del ser. Los tipos de verbos incluyen verbos de acción, verbos copulativos, verbos auxiliares, verbos regulares y verbos irregulares.',
                            'bn_excerpt' => 'ইংরেজি ব্যাকরণে ক্রিয়া এবং তাদের বিভিন্ন প্রকার সম্পর্কে জানুন।',
                            'hi_excerpt' => 'अंग्रेजी व्याकरण में क्रिया और उनके विभिन्न प्रकारों का अन्वेषण करें।',
                            'es_excerpt' => 'Explore los verbos y sus diferentes tipos en la gramática inglesa.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Tenses',
                'slug' => 'tenses',
                'content' => 'Tenses indicate when an action takes place: in the past, present, or future.',
                'display_order' => 2,
                'translation' => [
                    'bn_title' => 'কাল',
                    'hi_title' => 'काल',
                    'es_title' => 'Tiempos verbales',
                    'bn_content' => 'কাল নির্দেশ করে কখন একটি ক্রিয়া সংঘটিত হয়: অতীতে, বর্তমানে, বা ভবিষ্যতে।',
                    'hi_content' => 'काल इंगित करता है कि कोई क्रिया कब होती है: भूतकाल में, वर्तमान में, या भविष्य में।',
                    'es_content' => 'Los tiempos verbales indican cuándo se realiza una acción: en el pasado, presente o futuro.',
                ],
                'articles' => [
                    [
                        'type' => 'lesson',
                        'title' => 'Present Tense: Forms and Usage',
                        'slug' => 'present-tense-forms-and-usage',
                        'content' => 'The present tense is used to describe current actions, habitual actions, and general truths. It has four forms: simple present, present continuous, present perfect, and present perfect continuous.',
                        'display_order' => 1,
                        'excerpt' => 'Master the present tense in English with its four forms and usage rules.',
                        'is_premium' => true,
                        'translation' => [
                            'bn_title' => 'বর্তমান কাল: রূপ এবং ব্যবহার',
                            'hi_title' => 'वर्तमान काल: रूप और उपयोग',
                            'es_title' => 'Tiempo presente: Formas y uso',
                            'bn_content' => 'বর্তমান কাল বর্তমান ক্রিয়া, অভ্যাসগত ক্রিয়া এবং সাধারণ সত্য বর্ণনা করতে ব্যবহৃত হয়। এর চারটি রূপ রয়েছে: সাধারণ বর্তমান, বর্তমান চলমান, বর্তমান পূর্ণ এবং বর্তমান পূর্ণ চলমান।',
                            'hi_content' => 'वर्तमान काल का उपयोग वर्तमान क्रियाओं, आदतन क्रियाओं और सामान्य सत्य का वर्णन करने के लिए किया जाता है। इसके चार रूप हैं: सामान्य वर्तमान, वर्तमान निरंतर, वर्तमान पूर्ण और वर्तमान पूर्ण निरंतर।',
                            'es_content' => 'El tiempo presente se utiliza para describir acciones actuales, acciones habituales y verdades generales. Tiene cuatro formas: presente simple, presente continuo, presente perfecto y presente perfecto continuo.',
                            'bn_excerpt' => 'ইংরেজিতে বর্তমান কালের চারটি রূপ এবং ব্যবহারের নিয়ম সহ মাস্টার করুন।',
                            'hi_excerpt' => 'अंग्रेजी में वर्तमान काल को उसके चार रूपों और उपयोग के नियमों के साथ मास्टर करें।',
                            'es_excerpt' => 'Domine el tiempo presente en inglés con sus cuatro formas y reglas de uso.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $section = Section::create([
                'user_id' => $user->id,
                'series_id' => $englishGrammarSeries->id,
                'title' => $sectionData['title'],
                'slug' => $sectionData['slug'],
                'content' => $sectionData['content'],
                'display_order' => $sectionData['display_order'],
            ]);

            // Create section translation
            SectionTranslation::create([
                'section_id' => $section->id,
                'bn_title' => $sectionData['translation']['bn_title'],
                'hi_title' => $sectionData['translation']['hi_title'],
                'es_title' => $sectionData['translation']['es_title'],
                'bn_content' => $sectionData['translation']['bn_content'],
                'hi_content' => $sectionData['translation']['hi_content'],
                'es_content' => $sectionData['translation']['es_content'],
            ]);

            // Create articles for this section
            if (isset($sectionData['articles'])) {
                foreach ($sectionData['articles'] as $articleData) {
                    $article = Article::create([
                        'user_id' => $user->id,
                        'series_id' => $englishGrammarSeries->id,
                        'section_id' => $section->id,
                        'type' => $articleData['type'],
                        'title' => $articleData['title'],
                        'slug' => $articleData['slug'],
                        'content' => $articleData['content'],
                        'display_order' => $articleData['display_order'],
                        'excerpt' => $articleData['excerpt'],
                        'is_premium' => $articleData['is_premium'],
                        'scratchpad' => null,
                    ]);

                    // Create article translation
                    ArticleTranslation::create([
                        'article_id' => $article->id,
                        'bn_title' => $articleData['translation']['bn_title'],
                        'hi_title' => $articleData['translation']['hi_title'],
                        'es_title' => $articleData['translation']['es_title'],
                        'bn_content' => $articleData['translation']['bn_content'],
                        'hi_content' => $articleData['translation']['hi_content'],
                        'es_content' => $articleData['translation']['es_content'],
                        'bn_excerpt' => $articleData['translation']['bn_excerpt'],
                        'hi_excerpt' => $articleData['translation']['hi_excerpt'],
                        'es_excerpt' => $articleData['translation']['es_excerpt'],
                    ]);
                }
            }
        }
    }
}

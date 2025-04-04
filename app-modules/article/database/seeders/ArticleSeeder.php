<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Article\Models\Article;
use Modules\Article\Models\Course;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get course IDs
        $grammarCourseId = Course::where('slug', 'english-grammar-basics')->first()->id ?? 1;
        $idiomsCourseId = Course::where('slug', 'common-english-idioms')->first()->id ?? 2;
        
        // Create grammar articles
        $grammarArticles = [
            [
                'user_id' => 1,
                'course_id' => $grammarCourseId,

                'type' => 'section',
                'title' => 'Parts of Speech',
                'slug' => 'parts-of-speech',
                'content' => 'Learn about the different parts of speech in English.',
                'display_order' => 1,
                'status' => 'published',
                'excerpt' => 'Understanding the building blocks of English language.',
                'is_premium' => false,
                'title_translation' => [
                    'bn' => 'বাক্যের অংশ',
                    'hi' => 'वाक्य के भाग',
                ],
                'content_translation' => [
                    'bn' => 'ইংরেজিতে বিভিন্ন বাক্যের অংশ সম্পর্কে জানুন।',
                    'hi' => 'अंग्रेजी में विभिन्न वाक्य के भागों के बारे में जानें।',
                ],
                'excerpt_translation' => [
                    'bn' => 'ইংরেজি ভাষার বিল্ডিং ব্লকগুলি বোঝা।',
                    'hi' => 'अंग्रेजी भाषा के बिल्डिंग ब्लॉक्स को समझना।',
                ],
            ],
            [
                'user_id' => 1,
                'course_id' => $grammarCourseId,

                'type' => 'section',
                'title' => 'Tenses',
                'slug' => 'tenses',
                'content' => 'Learn about the different tenses in English.',
                'display_order' => 2,
                'status' => 'published',
                'excerpt' => 'Understanding time expressions in English.',
                'is_premium' => false,
                'title_translation' => [
                    'bn' => 'কাল',
                    'hi' => 'काल',
                ],
                'content_translation' => [
                    'bn' => 'ইংরেজিতে বিভিন্ন কাল সম্পর্কে জানুন।',
                    'hi' => 'अंग्रेजी में विभिन्न काल के बारे में जानें।',
                ],
                'excerpt_translation' => [
                    'bn' => 'ইংরেজিতে সময় প্রকাশের বিষয়গুলি বোঝা।',
                    'hi' => 'अंग्रेजी में समय अभिव्यक्तियों को समझना।',
                ],
            ],
        ];
        
        $idiomArticles = [
            [
                'user_id' => 1,
                'course_id' => $idiomsCourseId,

                'type' => 'section',
                'title' => 'Animal Idioms',
                'slug' => 'animal-idioms',
                'content' => 'Learn common idioms related to animals.',
                'display_order' => 1,
                'status' => 'published',
                'excerpt' => 'Explore idioms featuring animals and their meanings.',
                'is_premium' => false,
                'title_translation' => [
                    'bn' => 'প্রাণী সম্পর্কিত বাগধারা',
                    'hi' => 'जानवरों से संबंधित मुहावरे',
                ],
                'content_translation' => [
                    'bn' => 'প্রাণী সম্পর্কিত সাধারণ বাগধারা শিখুন।',
                    'hi' => 'जानवरों से संबंधित आम मुहावरे सीखें।',
                ],
                'excerpt_translation' => [
                    'bn' => 'প্রাণী সম্পর্কিত বাগধারা এবং তাদের অর্থ অন্বেষণ করুন।',
                    'hi' => 'जानवरों वाले मुहावरों और उनके अर्थों का पता लगाएं।',
                ],
            ],
        ];
        
        // Create articles
        foreach ($grammarArticles as $articleData) {
            Article::create($articleData);
        }
        
        foreach ($idiomArticles as $articleData) {
            Article::create($articleData);
        }
        
        // Create more grammar articles
        $articles = [
                [
                    'user_id' => 1,
                    'course_id' => $grammarCourseId,

                    'type' => 'tutorial',
                    'title' => 'Nouns',
                    'slug' => 'nouns',
                    'content' => 'A noun is a word that names a person, place, thing, or idea. Nouns can be common or proper, singular or plural, and concrete or abstract.',
                    'display_order' => 1,
                    'status' => 'published',
                    'excerpt' => 'Learn about nouns and their types.',
                    'is_premium' => false,
                    'title_translation' => [
                        'bn' => 'বিশেষ্য',
                        'hi' => 'संज्ञा',
                    ],
                    'content_translation' => [
                        'bn' => 'বিশেষ্য হল এমন একটি শব্দ যা কোনো ব্যক্তি, স্থান, জিনিস বা ধারণার নাম দেয়। বিশেষ্য সাধারণ বা নির্দিষ্ট, একবচন বা বহুবচন এবং মূর্ত বা বিমূর্ত হতে পারে।',
                        'hi' => 'संज्ञा एक ऐसा शब्द है जो किसी व्यक्ति, स्थान, वस्तु या विचार का नाम बताता है। संज्ञा सामान्य या विशेष, एकवचन या बहुवचन, और ठोस या अमूर्त हो सकती है।',
                    ],
                    'excerpt_translation' => [
                        'bn' => 'বিশেষ্য এবং তাদের প্রকারগুলি সম্পর্কে জানুন।',
                        'hi' => 'संज्ञा और उनके प्रकारों के बारे में जानें।',
                    ],
                ],
                [
                    'user_id' => 1,
                    'course_id' => $grammarCourseId,

                    'type' => 'tutorial',
                    'title' => 'Verbs',
                    'slug' => 'verbs',
                    'content' => 'A verb is a word that expresses an action, occurrence, or state of being. Verbs are essential components of sentences and can be categorized as action verbs, linking verbs, or helping verbs.',
                    'display_order' => 2,
                    'status' => 'published',
                    'excerpt' => 'Learn about verbs and their types.',
                    'is_premium' => false,
                    'title_translation' => [
                        'bn' => 'ক্রিয়া',
                        'hi' => 'क्रिया',
                    ],
                    'content_translation' => [
                        'bn' => 'ক্রিয়া হল এমন একটি শব্দ যা কোনো কাজ, ঘটনা বা অস্তিত্বের অবস্থা প্রকাশ করে। ক্রিয়াগুলি বাক্যের অপরিহার্য উপাদান এবং এগুলিকে কর্ম ক্রিয়া, সংযোগকারী ক্রিয়া বা সহায়ক ক্রিয়া হিসাবে শ্রেণীবদ্ধ করা যেতে পারে।',
                        'hi' => 'क्रिया एक ऐसा शब्द है जो किसी कार्य, घटना या अस्तित्व की स्थिति को व्यक्त करता है। क्रियाएँ वाक्यों के आवश्यक घटक हैं और इन्हें कार्य क्रिया, लिंकिंग क्रिया या सहायक क्रिया के रूप में वर्गीकृत किया जा सकता है।',
                    ],
                    'excerpt_translation' => [
                        'bn' => 'ক্রিয়া এবং তাদের প্রকারগুলি সম্পর্কে জানুন।',
                        'hi' => 'क्रिया और उनके प्रकारों के बारे में जानें।',
                    ],
                ],
            ];
            
            foreach ($articles as $articleData) {
                Article::create($articleData);
            }
        
        // Create more articles about tenses
        $articles = [
                [
                    'user_id' => 1,
                    'course_id' => $grammarCourseId,

                    'type' => 'tutorial',
                    'title' => 'Present Tense',
                    'slug' => 'present-tense',
                    'content' => 'The present tense is used to describe actions happening now or habitual actions. It includes simple present, present continuous, present perfect, and present perfect continuous.',
                    'display_order' => 1,
                    'status' => 'published',
                    'excerpt' => 'Learn about the present tense and its forms.',
                    'is_premium' => false,
                    'title_translation' => [
                        'bn' => 'বর্তমান কাল',
                        'hi' => 'वर्तमान काल',
                    ],
                    'content_translation' => [
                        'bn' => 'বর্তমান কাল ব্যবহার করা হয় এখন ঘটে যাওয়া ক্রিয়াকলাপ বা অভ্যাসগত ক্রিয়াকলাপ বর্ণনা করতে। এর মধ্যে রয়েছে সাধারণ বর্তমান, বর্তমান চলমান, বর্তমান পূর্ণ এবং বর্তমান পূর্ণ চলমান।',
                        'hi' => 'वर्तमान काल का उपयोग अब होने वाली क्रियाओं या आदतन क्रियाओं का वर्णन करने के लिए किया जाता है। इसमें सामान्य वर्तमान, वर्तमान निरंतर, वर्तमान पूर्ण और वर्तमान पूर्ण निरंतर शामिल हैं।',
                    ],
                    'excerpt_translation' => [
                        'bn' => 'বর্তমান কাল এবং এর রূপগুলি সম্পর্কে জানুন।',
                        'hi' => 'वर्तमान काल और उसके रूपों के बारे में जानें।',
                    ],
                ],
                [
                    'user_id' => 1,
                    'course_id' => $grammarCourseId,

                    'type' => 'tutorial',
                    'title' => 'Past Tense',
                    'slug' => 'past-tense',
                    'content' => 'The past tense is used to describe actions that happened in the past. It includes simple past, past continuous, past perfect, and past perfect continuous.',
                    'display_order' => 2,
                    'status' => 'published',
                    'excerpt' => 'Learn about the past tense and its forms.',
                    'is_premium' => false,
                    'title_translation' => [
                        'bn' => 'অতীত কাল',
                        'hi' => 'भूतकाल',
                    ],
                    'content_translation' => [
                        'bn' => 'অতীত কাল ব্যবহার করা হয় অতীতে ঘটে যাওয়া ক্রিয়াকলাপ বর্ণনা করতে। এর মধ্যে রয়েছে সাধারণ অতীত, অতীত চলমান, অতীত পূর্ণ এবং অতীত পূর্ণ চলমান।',
                        'hi' => 'भूतकाल का उपयोग अतीत में हुई क्रियाओं का वर्णन करने के लिए किया जाता है। इसमें सामान्य भूतकाल, भूतकाल निरंतर, भूतकाल पूर्ण और भूतकाल पूर्ण निरंतर शामिल हैं।',
                    ],
                    'excerpt_translation' => [
                        'bn' => 'অতীত কাল এবং এর রূপগুলি সম্পর্কে জানুন।',
                        'hi' => 'भूतकाल और उसके रूपों के बारे में जानें।',
                    ],
                ],
            ];
            
            foreach ($articles as $articleData) {
                Article::create($articleData);
            }
        
        // Create more idiom articles
        $articles = [
                [
                    'user_id' => 1,
                    'course_id' => $idiomsCourseId,

                    'type' => 'reference',
                    'title' => 'Cat Idioms',
                    'slug' => 'cat-idioms',
                    'content' => "Let the cat out of the bag: To reveal a secret accidentally.\nCat got your tongue: Used when someone is at a loss for words.\nLike a cat on hot bricks: To be restless or uneasy.\nCuriosity killed the cat: Being too curious can lead to trouble.",
                    'display_order' => 1,
                    'status' => 'published',
                    'excerpt' => 'Common English idioms related to cats.',
                    'is_premium' => false,
                    'title_translation' => [
                        'bn' => 'বিড়াল সম্পর্কিত বাগধারা',
                        'hi' => 'बिल्ली से संबंधित मुहावरे',
                    ],
                    'content_translation' => [
                        'bn' => "Let the cat out of the bag: দুর্ঘটনাক্রমে একটি গোপন প্রকাশ করা।\nCat got your tongue: যখন কেউ কথা বলতে পারে না।\nLike a cat on hot bricks: অস্থির বা অস্বস্তি বোধ করা।\nCuriosity killed the cat: অত্যধিক কৌতূহল বিপদে নিয়ে যেতে পারে।",
                        'hi' => "Let the cat out of the bag: गलती से किसी राज को उजागर करना।\nCat got your tongue: जब कोई बोल नहीं पाता है।\nLike a cat on hot bricks: बेचैन या असहज होना।\nCuriosity killed the cat: अत्यधिक जिज्ञासा परेशानी का कारण बन सकती है।",
                    ],
                    'excerpt_translation' => [
                        'bn' => 'বিড়াল সম্পর্কিত সাধারণ ইংরেজি বাগধারা।',
                        'hi' => 'बिल्लियों से संबंधित आम अंग्रेजी मुहावरे।',
                    ],
                ],
                [
                    'user_id' => 1,
                    'course_id' => $idiomsCourseId,

                    'type' => 'reference',
                    'title' => 'Dog Idioms',
                    'slug' => 'dog-idioms',
                    'content' => "Every dog has its day: Everyone will have good luck or success at some point.\nLet sleeping dogs lie: Don't disturb a situation that is currently causing no problems.\nDog-eat-dog world: A competitive and ruthless environment.\nGone to the dogs: Something that has deteriorated or been ruined.",
                    'display_order' => 2,
                    'status' => 'published',
                    'excerpt' => 'Common English idioms related to dogs.',
                    'is_premium' => false,
                    'title_translation' => [
                        'bn' => 'কুকুর সম্পর্কিত বাগধারা',
                        'hi' => 'कुत्ते से संबंधित मुहावरे',
                    ],
                    'content_translation' => [
                        'bn' => "Every dog has its day: প্রত্যেকেরই কোনো না কোনো সময় ভাগ্য বা সাফল্য আসবে।\nLet sleeping dogs lie: এমন পরিস্থিতি বিরক্ত করবেন না যা বর্তমানে কোনো সমস্যা সৃষ্টি করছে না।\nDog-eat-dog world: একটি প্রতিযোগিতামূলক এবং নিষ্ঠুর পরিবেশ।\nGone to the dogs: এমন কিছু যা অবনতি হয়েছে বা নষ্ট হয়ে গেছে।",
                        'hi' => "Every dog has its day: हर किसी को कभी न कभी अच्छी किस्मत या सफलता मिलेगी।\nLet sleeping dogs lie: ऐसी स्थिति को परेशान न करें जो वर्तमान में कोई समस्या नहीं पैदा कर रही है।\nDog-eat-dog world: एक प्रतिस्पर्धी और निर्दयी वातावरण।\nGone to the dogs: कुछ ऐसा जो बिगड़ गया है या बर्बाद हो गया है।",
                    ],
                    'excerpt_translation' => [
                        'bn' => 'কুকুর সম্পর্কিত সাধারণ ইংরেজি বাগধারা।',
                        'hi' => 'कुत्तों से संबंधित आम अंग्रेजी मुहावरे।',
                    ],
                ],
            ];
            
            foreach ($articles as $articleData) {
                Article::create($articleData);
            }
    }
}

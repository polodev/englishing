<?php

namespace Modules\Sentence\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Sentence\Models\Sentence;
use Modules\Sentence\Models\SentenceTranslation;

class SentenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample sentences
        $sentences = [
            [
                'sentence' => 'The quick brown fox jumps over the lazy dog.',
                'slug' => 'quick-brown-fox',
                'source' => 'Common English pangram',
                'pronunciation' => [
                    'bn' => 'দ্য কুইক ব্রাউন ফক্স জাম্পস ওভার দ্য লেজি ডগ',
                    'hi' => 'द क्विक ब्राउन फॉक्स जम्प्स ओवर द लेज़ी डॉग'
                ],
                'translations' => [
                    [
                        'locale' => 'bn',
                        'translation' => 'দ্রুত বাদামী শিয়াল অলস কুকুরের উপর দিয়ে লাফিয়ে যায়।',
                        'transliteration' => 'Druto badami shiyal olosh kukurer upor diye laphiye jay.',
                        'slug' => 'quick-brown-fox-bn',
                        'source' => 'Manual translation'
                    ],
                    [
                        'locale' => 'hi',
                        'translation' => 'तेज़ भूरी लोमड़ी आलसी कुत्ते के ऊपर से कूदती है।',
                        'transliteration' => 'Tez bhoori lomadi aalasi kutte ke upar se koodti hai.',
                        'slug' => 'quick-brown-fox-hi',
                        'source' => 'Manual translation'
                    ]
                ]
            ],
            [
                'sentence' => 'All human beings are born free and equal in dignity and rights.',
                'slug' => 'human-rights-declaration',
                'source' => 'Universal Declaration of Human Rights',
                'pronunciation' => [
                    'bn' => 'অল হিউম্যান বিয়িংস আর বর্ন ফ্রি এন্ড ইকুয়াল ইন ডিগনিটি এন্ড রাইটস',
                    'hi' => 'ऑल ह्यूमन बीइंग्स आर बॉर्न फ्री ऐंड ईक्वल इन डिग्निटी ऐंड राइट्स'
                ],
                'translations' => [
                    [
                        'locale' => 'bn',
                        'translation' => 'সমস্ত মানুষ স্বাধীন এবং মর্যাদা ও অধিকারে সমান হয়ে জন্মগ্রহণ করে।',
                        'transliteration' => 'Somosto manush swadhin ebong morjada o odhikare soman hoye jonmogrohon kore.',
                        'slug' => 'human-rights-declaration-bn',
                        'source' => 'Official UDHR translation'
                    ],
                    [
                        'locale' => 'hi',
                        'translation' => 'सभी मनुष्य जन्म से स्वतंत्र तथा गरिमा और अधिकारों में समान होते हैं।',
                        'transliteration' => 'Sabhi manushya janm se swatantra tatha garima aur adhikaron mein saman hote hain.',
                        'slug' => 'human-rights-declaration-hi',
                        'source' => 'Official UDHR translation'
                    ]
                ]
            ],
            [
                'sentence' => 'The sun rises in the east and sets in the west.',
                'slug' => 'sun-rises-east',
                'source' => 'Common knowledge',
                'pronunciation' => [
                    'bn' => 'দ্য সান রাইজেস ইন দ্য ইস্ট এন্ড সেটস ইন দ্য ওয়েস্ট',
                    'hi' => 'द सन राइज़ेज़ इन द ईस्ट ऐंड सेट्स इन द वेस्ट'
                ],
                'translations' => [
                    [
                        'locale' => 'bn',
                        'translation' => 'সূর্য পূর্বে ওঠে এবং পশ্চিমে অস্ত যায়।',
                        'transliteration' => 'Surjo purbe othe ebong poshchime osto jay.',
                        'slug' => 'sun-rises-east-bn',
                        'source' => 'Manual translation'
                    ],
                    [
                        'locale' => 'hi',
                        'translation' => 'सूरज पूरब में उगता है और पश्चिम में डूबता है।',
                        'transliteration' => 'Suraj purab mein ugta hai aur paschim mein dubta hai.',
                        'slug' => 'sun-rises-east-hi',
                        'source' => 'Manual translation'
                    ]
                ]
            ]
        ];

        // Create sentences and their translations
        foreach ($sentences as $sentenceData) {
            $translations = $sentenceData['translations'] ?? [];
            unset($sentenceData['translations']);

            $sentence = Sentence::create($sentenceData);

            // Create translations
            foreach ($translations as $translationData) {
                $sentence->translations()->create($translationData);
            }
        }
    }
}

<?php

namespace Modules\Expression\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Expression\Models\Expression;
use Modules\Expression\Models\ExpressionMeaning;
use Modules\Expression\Models\ExpressionTranslation;

class ExpressionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample expressions
        $expressions = [
            [
                'expression' => 'Break a leg',
                'type' => 'idiom',
                'slug' => 'break-a-leg',
                'meanings' => [
                    [
                        'meaning' => 'Good luck (often said to performers before they go on stage)',
                        'pronunciation' => ['en' => 'breɪk ə lɛg', 'bn' => 'ব্রেক এ লেগ', 'hi' => 'ब्रेक अ लेग'],
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'শুভকামনা',
                                'transliteration' => 'Shubhokamona',
                                'slug' => 'break-a-leg-bn',
                                'source' => 'Manual translation'
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'शुभकामनाएं',
                                'transliteration' => 'Shubhkamnayen',
                                'slug' => 'break-a-leg-hi',
                                'source' => 'Manual translation'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'expression' => 'Piece of cake',
                'type' => 'idiom',
                'slug' => 'piece-of-cake',
                'meanings' => [
                    [
                        'meaning' => 'Something that is very easy to do',
                        'pronunciation' => ['en' => 'piːs əv keɪk', 'bn' => 'পিস অফ কেক', 'hi' => 'पीस ऑफ केक'],
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'খুব সহজ',
                                'transliteration' => 'Khub sohoj',
                                'slug' => 'piece-of-cake-bn',
                                'source' => 'Manual translation'
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'बहुत आसान',
                                'transliteration' => 'Bahut aasan',
                                'slug' => 'piece-of-cake-hi',
                                'source' => 'Manual translation'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'expression' => 'Hit the nail on the head',
                'type' => 'idiom',
                'slug' => 'hit-the-nail-on-the-head',
                'meanings' => [
                    [
                        'meaning' => 'To describe exactly what is causing a situation or problem',
                        'pronunciation' => ['en' => 'hɪt ðə neɪl ɒn ðə hɛd', 'bn' => 'হিট দ্য নেইল অন দ্য হেড', 'hi' => 'हिट द नेल ऑन द हेड'],
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'সঠিক বিষয়টি চিহ্নিত করা',
                                'transliteration' => 'Shothik bishoyti chinhito kora',
                                'slug' => 'hit-the-nail-on-the-head-bn',
                                'source' => 'Manual translation'
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'सही बात पहचानना',
                                'transliteration' => 'Sahi baat pehchanna',
                                'slug' => 'hit-the-nail-on-the-head-hi',
                                'source' => 'Manual translation'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'expression' => 'A piece of cake',
                'type' => 'idiom',
                'slug' => 'a-piece-of-cake',
                'meanings' => [
                    [
                        'meaning' => 'Something that is very easy to do',
                        'pronunciation' => ['en' => 'ə piːs əv keɪk', 'bn' => 'এ পিস অফ কেক', 'hi' => 'अ पीस ऑफ केक'],
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'খুব সহজ',
                                'transliteration' => 'Khub sohoj',
                                'slug' => 'a-piece-of-cake-bn',
                                'source' => 'Manual translation'
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'बहुत आसान',
                                'transliteration' => 'Bahut aasan',
                                'slug' => 'a-piece-of-cake-hi',
                                'source' => 'Manual translation'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Create expressions and their relationships
        foreach ($expressions as $expressionData) {
            $meanings = $expressionData['meanings'] ?? [];
            unset($expressionData['meanings']);

            $expression = Expression::create($expressionData);

            // Create meanings
            foreach ($meanings as $meaningData) {
                $translations = $meaningData['translations'] ?? [];
                unset($meaningData['translations']);

                $meaning = $expression->meanings()->create($meaningData);

                // Create translations
                foreach ($translations as $translationData) {
                    $translationData['expression_id'] = $expression->id;
                    $meaning->translations()->create($translationData);
                }
            }
        }

        // Create expression connections (synonyms and antonyms)
        $pieceOfCake = Expression::where('slug', 'piece-of-cake')->first();
        $aPieceOfCake = Expression::where('slug', 'a-piece-of-cake')->first();
        $breakALeg = Expression::where('slug', 'break-a-leg')->first();
        $hitTheNail = Expression::where('slug', 'hit-the-nail-on-the-head')->first();

        if ($pieceOfCake && $aPieceOfCake) {
            $pieceOfCake->attachSynonym($aPieceOfCake);
        }

        if ($breakALeg && $hitTheNail) {
            // These aren't really antonyms but for demonstration purposes
            $breakALeg->attachAntonym($hitTheNail);
        }
    }
}

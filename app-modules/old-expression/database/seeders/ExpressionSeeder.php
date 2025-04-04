<?php

namespace Modules\Expression\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Expression\Models\Expression;
use Modules\Expression\Models\ExpressionConnection;
use Modules\Expression\Models\ExpressionMeaning;
use Modules\Expression\Models\ExpressionMeaningTranslation;
use Modules\Expression\Models\ExpressionMeaningTransliteration;
use Modules\Expression\Models\ExpressionPronunciation;

class ExpressionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample expressions
        $expressions = [
            [
                'expression' => 'kick the bucket',
                'type' => 'idiom',
                'slug' => 'kick-the-bucket',
                'meanings' => [
                    [
                        'meaning' => 'To die',
                        'translation' => [
                            'bn_meaning' => 'মৃত্যুবরণ করা',
                            'hi_meaning' => 'मर जाना',
                            'es_meaning' => 'morir',
                            'transliteration' => [
                                'bn_transliteration' => 'mrityuboron kora',
                                'hi_transliteration' => 'mar jana',
                                'es_transliteration' => 'morir',
                            ],
                        ],
                    ],
                ],
                'pronunciation' => [
                    'bn_pronunciation' => 'কিক দ্য বাকেট',
                    'hi_pronunciation' => 'किक द बकेट',
                    'es_pronunciation' => 'kik da baket',
                ],
            ],
            [
                'expression' => 'break a leg',
                'type' => 'idiom',
                'slug' => 'break-a-leg',
                'meanings' => [
                    [
                        'meaning' => 'Good luck (often said to performers before they go on stage)',
                        'translation' => [
                            'bn_meaning' => 'শুভকামনা',
                            'hi_meaning' => 'शुभकामनाएं',
                            'es_meaning' => 'buena suerte',
                            'transliteration' => [
                                'bn_transliteration' => 'shubhokamona',
                                'hi_transliteration' => 'shubhkamnayen',
                                'es_transliteration' => 'buena suerte',
                            ],
                        ],
                    ],
                ],
                'pronunciation' => [
                    'bn_pronunciation' => 'ব্রেক এ লেগ',
                    'hi_pronunciation' => 'ब्रेक अ लेग',
                    'es_pronunciation' => 'breik a leg',
                ],
            ],
            [
                'expression' => 'pass away',
                'type' => 'phrasal verb',
                'slug' => 'pass-away',
                'meanings' => [
                    [
                        'meaning' => 'To die (euphemism)',
                        'translation' => [
                            'bn_meaning' => 'মৃত্যুবরণ করা',
                            'hi_meaning' => 'मृत्यु हो जाना',
                            'es_meaning' => 'fallecer',
                            'transliteration' => [
                                'bn_transliteration' => 'mrityuboron kora',
                                'hi_transliteration' => 'mrityu ho jana',
                                'es_transliteration' => 'fallecer',
                            ],
                        ],
                    ],
                ],
                'pronunciation' => [
                    'bn_pronunciation' => 'পাস অ্যাওয়ে',
                    'hi_pronunciation' => 'पास अवे',
                    'es_pronunciation' => 'pas auei',
                ],
            ],
        ];

        // Create expressions with their relationships
        foreach ($expressions as $expressionData) {
            $expression = Expression::create([
                'expression' => $expressionData['expression'],
                'type' => $expressionData['type'],
                'slug' => $expressionData['slug'],
            ]);

            // Create meanings
            foreach ($expressionData['meanings'] as $meaningData) {
                $meaning = ExpressionMeaning::create([
                    'expression_id' => $expression->id,
                    'meaning' => $meaningData['meaning'],
                ]);

                // Create translation (only one per meaning)
                if (!empty($meaningData['translation'])) {
                    $translation = ExpressionMeaningTranslation::create([
                        'expression_meaning_id' => $meaning->id,
                        'bn_meaning' => $meaningData['translation']['bn_meaning'],
                        'hi_meaning' => $meaningData['translation']['hi_meaning'],
                        'es_meaning' => $meaningData['translation']['es_meaning'],
                    ]);

                    // Create transliteration (only one per translation)
                    if (!empty($meaningData['translation']['transliteration'])) {
                        ExpressionMeaningTransliteration::create([
                            'expression_meaning_translation_id' => $translation->id,
                            'bn_transliteration' => $meaningData['translation']['transliteration']['bn_transliteration'],
                            'hi_transliteration' => $meaningData['translation']['transliteration']['hi_transliteration'],
                            'es_transliteration' => $meaningData['translation']['transliteration']['es_transliteration'],
                        ]);
                    }
                }
            }

            // Create pronunciation (only one per expression)
            if (!empty($expressionData['pronunciation'])) {
                ExpressionPronunciation::create([
                    'expression_id' => $expression->id,
                    'bn_pronunciation' => $expressionData['pronunciation']['bn_pronunciation'],
                    'hi_pronunciation' => $expressionData['pronunciation']['hi_pronunciation'],
                    'es_pronunciation' => $expressionData['pronunciation']['es_pronunciation'],
                ]);
            }
        }

        // Create expression connections
        $kickTheBucket = Expression::where('expression', 'kick the bucket')->first();
        $passAway = Expression::where('expression', 'pass away')->first();

        // Create synonym connection
        ExpressionConnection::create([
            'expression_id_1' => $kickTheBucket->id,
            'expression_id_2' => $passAway->id,
            'type' => 'synonyms',
        ]);
    }
}

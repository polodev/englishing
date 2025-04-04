<?php

namespace Modules\Word\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Word\Models\Word;
use Modules\Word\Models\WordConnection;
use Modules\Word\Models\WordMeaning;
use Modules\Word\Models\WordMeaningTranslation;
use Modules\Word\Models\WordMeaningTransliteration;
use Modules\Word\Models\WordPronunciation;

class WordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample words
        $words = [
            [
                'word' => 'happy',
                'slug' => 'happy',
                'meanings' => [
                    [
                        'meaning' => 'feeling or showing pleasure or contentment',
                        'slug' => 'happy-feeling-pleasure',
                        'translations' => [
                            [
                                'bn_meaning' => 'সুখী',
                                'hi_meaning' => 'खुश',
                                'es_meaning' => 'feliz',
                                'transliterations' => [
                                    [
                                        'bn_transliteration' => 'shukhi',
                                        'hi_transliteration' => 'khush',
                                        'es_transliteration' => 'feliz',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'meaning' => 'fortunate and convenient',
                        'slug' => 'happy-fortunate',
                        'translations' => [
                            [
                                'bn_meaning' => 'সৌভাগ্যবান',
                                'hi_meaning' => 'भाग्यशाली',
                                'es_meaning' => 'afortunado',
                                'transliterations' => [
                                    [
                                        'bn_transliteration' => 'soubhagyaban',
                                        'hi_transliteration' => 'bhagyashali',
                                        'es_transliteration' => 'afortunado',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'pronunciations' => [
                    [
                        'bn_pronunciation' => 'হ্যাপি',
                        'hi_pronunciation' => 'हैप्पी',
                        'es_pronunciation' => 'japi',
                    ],
                ],
            ],
            [
                'word' => 'sad',
                'slug' => 'sad',
                'meanings' => [
                    [
                        'meaning' => 'feeling or showing sorrow; unhappy',
                        'slug' => 'sad-feeling-sorrow',
                        'translations' => [
                            [
                                'bn_meaning' => 'দুঃখিত',
                                'hi_meaning' => 'दुखी',
                                'es_meaning' => 'triste',
                                'transliterations' => [
                                    [
                                        'bn_transliteration' => 'dukhito',
                                        'hi_transliteration' => 'dukhi',
                                        'es_transliteration' => 'triste',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'pronunciations' => [
                    [
                        'bn_pronunciation' => 'স্যাড',
                        'hi_pronunciation' => 'सैड',
                        'es_pronunciation' => 'sad',
                    ],
                ],
            ],
            [
                'word' => 'joyful',
                'slug' => 'joyful',
                'meanings' => [
                    [
                        'meaning' => 'feeling, expressing, or causing great pleasure and happiness',
                        'slug' => 'joyful-feeling-pleasure',
                        'translations' => [
                            [
                                'bn_meaning' => 'আনন্দময়',
                                'hi_meaning' => 'आनंदमय',
                                'es_meaning' => 'alegre',
                                'transliterations' => [
                                    [
                                        'bn_transliteration' => 'anandomoy',
                                        'hi_transliteration' => 'anandmay',
                                        'es_transliteration' => 'alegre',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'pronunciations' => [
                    [
                        'bn_pronunciation' => 'জয়ফুল',
                        'hi_pronunciation' => 'जॉयफुल',
                        'es_pronunciation' => 'yoiful',
                    ],
                ],
            ],
        ];

        // Create words with their relationships
        foreach ($words as $wordData) {
            $word = Word::create([
                'word' => $wordData['word'],
                'slug' => $wordData['slug'],
            ]);

            // Create meanings
            foreach ($wordData['meanings'] as $meaningData) {
                $meaning = WordMeaning::create([
                    'word_id' => $word->id,
                    'meaning' => $meaningData['meaning'],
                    'slug' => $meaningData['slug'],
                ]);

                // Create translation (only one per meaning)
                if (!empty($meaningData['translations'])) {
                    $translationData = $meaningData['translations'][0];
                    $translation = WordMeaningTranslation::create([
                        'word_meaning_id' => $meaning->id,
                        'bn_meaning' => $translationData['bn_meaning'],
                        'hi_meaning' => $translationData['hi_meaning'],
                        'es_meaning' => $translationData['es_meaning'],
                    ]);

                    // Create transliteration (only one per translation)
                    if (!empty($translationData['transliterations'])) {
                        $transliterationData = $translationData['transliterations'][0];
                        WordMeaningTransliteration::create([
                            'word_meaning_translation_id' => $translation->id,
                            'bn_transliteration' => $transliterationData['bn_transliteration'],
                            'hi_transliteration' => $transliterationData['hi_transliteration'],
                            'es_transliteration' => $transliterationData['es_transliteration'],
                        ]);
                    }
                }
            }

            // Create pronunciation (only one per word)
            if (!empty($wordData['pronunciations'])) {
                $pronunciationData = $wordData['pronunciations'][0];
                WordPronunciation::create([
                    'word_id' => $word->id,
                    'bn_pronunciation' => $pronunciationData['bn_pronunciation'],
                    'hi_pronunciation' => $pronunciationData['hi_pronunciation'],
                    'es_pronunciation' => $pronunciationData['es_pronunciation'],
                ]);
            }
        }

        // Create word connections
        $happyWord = Word::where('word', 'happy')->first();
        $joyfulWord = Word::where('word', 'joyful')->first();
        $sadWord = Word::where('word', 'sad')->first();

        // Create synonym connection
        WordConnection::create([
            'word_id_1' => $happyWord->id,
            'word_id_2' => $joyfulWord->id,
            'type' => 'synonyms',
        ]);

        // Create antonym connection
        WordConnection::create([
            'word_id_1' => $happyWord->id,
            'word_id_2' => $sadWord->id,
            'type' => 'antonyms',
        ]);
    }
}

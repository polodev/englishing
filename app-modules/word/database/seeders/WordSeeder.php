<?php

namespace Modules\Word\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Word\Models\Word;
use Modules\Word\Models\WordMeaning;
use Modules\Word\Models\WordTranslation;

class WordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample words
        $words = [
            [
                'word' => 'Happy',
                'slug' => 'happy',
                'pronunciation' => ['en' => 'ˈhæpi', 'bn' => 'হ্যাপি', 'hi' => 'हैपी'],
                'phonetic' => 'ˈhæpi',
                'part_of_speech' => 'adjective',
                'source' => 'Oxford Dictionary',
                'meanings' => [
                    [
                        'meaning' => 'feeling or showing pleasure or contentment',
                        'slug' => 'feeling-pleasure',
                        'source' => 'Oxford Dictionary',
                        'display_order' => 1,
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'সুখী',
                                'transliteration' => 'shukhi',
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'खुश',
                                'transliteration' => 'khush',
                            ],
                        ]
                    ],
                    [
                        'meaning' => 'fortunate and convenient',
                        'slug' => 'fortunate',
                        'source' => 'Oxford Dictionary',
                        'display_order' => 2,
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'সৌভাগ্যবান',
                                'transliteration' => 'soubhaggaban',
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'भाग्यशाली',
                                'transliteration' => 'bhagyashali',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'word' => 'Sad',
                'slug' => 'sad',
                'pronunciation' => ['en' => 'sæd', 'bn' => 'স্যাড', 'hi' => 'सैड'],
                'phonetic' => 'sæd',
                'part_of_speech' => 'adjective',
                'source' => 'Oxford Dictionary',
                'meanings' => [
                    [
                        'meaning' => 'feeling or showing sorrow; unhappy',
                        'slug' => 'feeling-sorrow',
                        'source' => 'Oxford Dictionary',
                        'display_order' => 1,
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'দুঃখিত',
                                'transliteration' => 'dukhito',
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'दुखी',
                                'transliteration' => 'dukhi',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'word' => 'Joyful',
                'slug' => 'joyful',
                'pronunciation' => ['en' => 'ˈdʒɔɪfʊl', 'bn' => 'জয়ফুল', 'hi' => 'जॉयफुल'],
                'phonetic' => 'ˈdʒɔɪfʊl',
                'part_of_speech' => 'adjective',
                'source' => 'Oxford Dictionary',
                'meanings' => [
                    [
                        'meaning' => 'feeling, expressing, or causing great pleasure and happiness',
                        'slug' => 'feeling-great-pleasure',
                        'source' => 'Oxford Dictionary',
                        'display_order' => 1,
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'আনন্দময়',
                                'transliteration' => 'anandomoy',
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'आनंदित',
                                'transliteration' => 'anandit',
                            ],
                        ]
                    ],
                ]
            ],
            [
                'word' => 'Miserable',
                'slug' => 'miserable',
                'pronunciation' => ['en' => 'ˈmɪz(ə)rəb(ə)l', 'bn' => 'মিজারেবল', 'hi' => 'मिज़रेबल'],
                'phonetic' => 'ˈmɪz(ə)rəb(ə)l',
                'part_of_speech' => 'adjective',
                'source' => 'Oxford Dictionary',
                'meanings' => [
                    [
                        'meaning' => 'extremely unhappy or uncomfortable',
                        'slug' => 'extremely-unhappy',
                        'source' => 'Oxford Dictionary',
                        'display_order' => 1,
                        'translations' => [
                            [
                                'locale' => 'bn',
                                'translation' => 'হতভাগ্য',
                                'transliteration' => 'hotobhaggo',
                            ],
                            [
                                'locale' => 'hi',
                                'translation' => 'दयनीय',
                                'transliteration' => 'dayaniy',
                            ],
                        ]
                    ],
                ]
            ],
        ];

        // Create words and their relationships
        foreach ($words as $wordData) {
            $meanings = $wordData['meanings'] ?? [];
            unset($wordData['meanings']);

            $word = Word::create($wordData);

            // Create meanings
            foreach ($meanings as $meaningData) {
                $translations = $meaningData['translations'] ?? [];
                unset($meaningData['translations']);

                $meaning = $word->meanings()->create($meaningData);

                // Create translations
                foreach ($translations as $translationData) {
                    $translationData['word_id'] = $word->id;
                    $meaning->translations()->create($translationData);
                }
            }
        }

        // Create word connections (synonyms and antonyms)
        $happy = Word::where('slug', 'happy')->first();
        $joyful = Word::where('slug', 'joyful')->first();
        $sad = Word::where('slug', 'sad')->first();
        $miserable = Word::where('slug', 'miserable')->first();

        if ($happy && $joyful) {
            $happy->attachSynonym($joyful);
        }

        if ($sad && $miserable) {
            $sad->attachSynonym($miserable);
        }

        if ($happy && $sad) {
            $happy->attachAntonym($sad);
        }

        if ($joyful && $miserable) {
            $joyful->attachAntonym($miserable);
        }
    }
}

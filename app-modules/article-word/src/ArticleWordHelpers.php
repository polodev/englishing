<?php

namespace Modules\ArticleWord;

use Modules\ArticleWord\Models\ArticleWordSet;
use Modules\ArticleWord\Models\ArticleWordSetList;
use Modules\ArticleWord\Models\ArticleWordTranslation;

class ArticleWordHelpers
{
    /**
     * Generate JSON representation of the word set list items
     *
     * @param ArticleWordSet $wordSet
     * @param array $supportedLocales The supported locales for translations (default: ['bn', 'hi'])
     * @return array
     */
    public static function generateJsonData(ArticleWordSet $wordSet, array $supportedLocales = ['bn', 'hi'])
    {
        // Get the article word set data
        $wordSetLists = ArticleWordSetList::where('article_word_set_id', $wordSet->id)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        // Initialize the data structure with word set information
        $data = [
            'id' => $wordSet->id,
            'article_id' => $wordSet->article_id,
            'title' => $wordSet->title,
            'content' => $wordSet->content,
            'display_order' => $wordSet->display_order,
            'static_content_1' => $wordSet->static_content_1 ?? '',
            'static_content_2' => $wordSet->static_content_2 ?? '',
            'title_translation' => [
                'bn' => $wordSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $wordSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $wordSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $wordSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($wordSet->column_order) ? json_decode($wordSet->column_order, true) : ($wordSet->column_order ?? ['word', 'phonetic', 'meaning', 'example_sentence']),
            'word_set_lists' => []
        ];

        if ($wordSetLists->isNotEmpty()) {
            foreach ($wordSetLists as $wordSetList) {
                $translations = $wordSetList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'word_translation' => $translation->word_translation ?? '',
                        'word_transliteration' => $translation->word_transliteration ?? '',
                        'example_sentence_translation' => $translation->example_sentence_translation ?? '',
                        'example_sentence_transliteration' => $translation->example_sentence_transliteration ?? '',
                        'example_expression_translation' => $translation->example_expression_translation ?? '',
                        'example_expression_transliteration' => $translation->example_expression_transliteration ?? '',
                        'source' => $translation->source ?? 'oxford'
                    ];
                }

                // Get pronunciation data using Spatie's getTranslation method
                $pronunciation = [
                    'bn' => $wordSetList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $wordSetList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                // Format synonyms and antonyms (could be string or array in JSON)
                $synonyms = $wordSetList->synonyms;
                $antonyms = $wordSetList->antonyms;

                $data['word_set_lists'][] = [
                    'id' => $wordSetList->id,
                    'word' => $wordSetList->word,
                    'slug' => $wordSetList->slug,
                    'phonetic' => $wordSetList->phonetic,
                    'pronunciation' => $pronunciation,
                    'parts_of_speech' => $wordSetList->parts_of_speech,
                    'static_content_1' => $wordSetList->static_content_1,
                    'static_content_2' => $wordSetList->static_content_2,
                    'meaning' => $wordSetList->meaning,
                    'example_sentence' => $wordSetList->example_sentence,
                    'example_expression' => $wordSetList->example_expression,
                    'example_expression_meaning' => $wordSetList->example_expression_meaning,
                    'display_order' => $wordSetList->display_order,
                    'synonyms' => $synonyms,
                    'antonyms' => $antonyms,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['word_set_lists'] = [
                [
                    'word' => '',
                    'phonetic' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'parts_of_speech' => '',
                    'static_content_1' => '',
                    'static_content_2' => '',
                    'meaning' => '',
                    'example_sentence' => '',
                    'example_expression' => '',
                    'example_expression_meaning' => '',
                    'display_order' => 1,
                    'synonyms' => '',
                    'antonyms' => '',
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'word_translation' => '',
                            'word_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'example_expression_translation' => '',
                            'example_expression_transliteration' => '',
                            'source' => 'oxford'
                        ],
                        [
                            'locale' => 'hi',
                            'word_translation' => '',
                            'word_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'example_expression_translation' => '',
                            'example_expression_transliteration' => '',
                            'source' => 'oxford'
                        ]
                    ]
                ]
            ];
        }

        return $data;
    }

    /**
     * Process JSON data to update or create word list entries
     *
     * @param ArticleWordSet|null $wordSet Existing word set or null to create a new one
     * @param string $jsonData
     * @param array $debugInfo Optional array to store debug information
     * @return ArticleWordSet The processed word set instance (existing or newly created)
     * @throws \Exception If there are issues processing the data
     */
    public static function processJsonData(?ArticleWordSet $wordSet, $jsonData, array &$debugInfo = [])
    {
        // Decode JSON data
        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = "Invalid JSON: " . json_last_error_msg();
            if (is_array($debugInfo)) {
                $debugInfo[] = $error;
            }
            throw new \Exception($error);
        }

        // Validate the JSON structure
        if (!is_array($data)) {
            throw new \Exception("JSON must be an object with word set data");
        }

        // If wordSet is null, create a new ArticleWordSet instance
        $creatingNew = false;
        if ($wordSet === null) {
            $creatingNew = true;
            $wordSet = new ArticleWordSet();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Creating new ArticleWordSet instance";
            }
        } else {
            // Debug info for existing word set
            if (is_array($debugInfo)) {
                $debugInfo[] = "Using article_word_set_id: {$wordSet->id}";
            }
        }

        // 1. Update or create the Article Word Set (parent) data
        if (isset($data['title']) || $creatingNew) {
            // Process column_order - ensure it's stored as a JSON string in the database
            $columnOrder = null;
            if (isset($data['column_order'])) {
                // Ensure column_order is saved as a JSON string, not an array
                $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
            }

            // If creating new, set the article_id if available
            $wordSetData = [
                'title' => $data['title'] ?? ($creatingNew ? '' : $wordSet->title),
                'content' => $data['content'] ?? ($creatingNew ? '' : $wordSet->content),
                'display_order' => $data['display_order'] ?? ($creatingNew ? 1 : $wordSet->display_order),
                'static_content_1' => $data['static_content_1'] ?? null,
                'static_content_2' => $data['static_content_2'] ?? null,
                'column_order' => $columnOrder,
            ];

            // Add article_id only when creating new
            if ($creatingNew && isset($data['article_id'])) {
                $wordSetData['article_id'] = $data['article_id'];
            }

            if ($creatingNew) {
                // Set all fields on the new model
                foreach ($wordSetData as $key => $value) {
                    $wordSet->$key = $value;
                }
                // Save the new word set
                $wordSet->save();
            } else {
                // Update the existing word set
                $wordSet->update($wordSetData);
            }

            if (is_array($debugInfo)) {
                $debugInfo[] = "Updated ArticleWordSet ID: {$wordSet->id}";
            }

            // Handle title_translation directly for bn and hi
            if (isset($data['title_translation'])) {
                // Set Bengali title translation
                if (isset($data['title_translation']['bn'])) {
                    $wordSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                }

                // Set Hindi title translation
                if (isset($data['title_translation']['hi'])) {
                    $wordSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
            }

            // Handle content_translation directly for bn and hi
            if (isset($data['content_translation'])) {
                // Set Bengali content translation
                if (isset($data['content_translation']['bn'])) {
                    $wordSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                }

                // Set Hindi content translation
                if (isset($data['content_translation']['hi'])) {
                    $wordSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
            }

            // Save translations
            $wordSet->save();
        }

        // 2. Process the word set list items
        if (isset($data['word_set_lists']) && is_array($data['word_set_lists'])) {
            $wordSetListData = $data['word_set_lists'];

            // Track processed IDs to identify which items to delete
            $processedIds = [];

            foreach ($wordSetListData as $index => $wordData) {
                // Skip if word is empty
                if (empty($wordData['word'])) {
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Skipping item at index $index: 'word' field is empty";
                    }
                    continue;
                }

                $word = $wordData['word'];
                // Always generate the slug from the word, ignoring any provided slug
                $slug = \Illuminate\Support\Str::slug($word);

                if (is_array($debugInfo)) {
                    $debugInfo[] = "Processing word: $word (Slug: $slug)";
                }

                // Prepare ArticleWordSetList data
                $listData = [
                    'word' => $word,
                    'slug' => $slug, // Generated slug
                    'phonetic' => $wordData['phonetic'] ?? null,
                    // We'll manually handle pronunciation after model creation
                    'parts_of_speech' => $wordData['parts_of_speech'] ?? null,
                    'static_content_1' => $wordData['static_content_1'] ?? null,
                    'static_content_2' => $wordData['static_content_2'] ?? null,
                    'meaning' => $wordData['meaning'] ?? null,
                    'example_sentence' => $wordData['example_sentence'] ?? null,
                    'example_expression' => $wordData['example_expression'] ?? null,
                    'example_expression_meaning' => $wordData['example_expression_meaning'] ?? null,
                    'display_order' => $wordData['display_order'] ?? ($index + 1) * 10,
                    'article_word_set_id' => $wordSet->id, // Ensure this is explicitly set
                    // Add synonyms and antonyms handling
                    'synonyms' => is_array($wordData['synonyms']) ? implode(', ', $wordData['synonyms']) : ($wordData['synonyms'] ?? null),
                    'antonyms' => is_array($wordData['antonyms']) ? implode(', ', $wordData['antonyms']) : ($wordData['antonyms'] ?? null)
                ];

                // Update or create word set list
                $wordSetList = ArticleWordSetList::updateOrCreate(
                    [
                        'article_word_set_id' => $wordSet->id,
                        'slug' => $slug
                    ],
                    $listData
                );

                if (is_array($debugInfo)) {
                    $debugInfo[] = "Updated/Created ArticleWordSetList ID: {$wordSetList->id} with article_word_set_id: {$wordSetList->article_word_set_id}";
                }

                // Track which IDs we've processed
                $processedIds[] = $wordSetList->id;

                // Set pronunciation data using Spatie's setTranslation method
                if (isset($wordData['pronunciation']) && is_array($wordData['pronunciation'])) {
                    // Set Bengali pronunciation
                    if (isset($wordData['pronunciation']['bn'])) {
                        $wordSetList->setTranslation('pronunciation', 'bn', $wordData['pronunciation']['bn']);
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Set Bengali pronunciation: {$wordData['pronunciation']['bn']}";
                        }
                    }

                    // Set Hindi pronunciation
                    if (isset($wordData['pronunciation']['hi'])) {
                        $wordSetList->setTranslation('pronunciation', 'hi', $wordData['pronunciation']['hi']);
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Set Hindi pronunciation: {$wordData['pronunciation']['hi']}";
                        }
                    }

                    // Save the model with the updated translations
                    $wordSetList->save();

                    // Verify the saved data
                    if (is_array($debugInfo)) {
                        $savedBn = $wordSetList->getTranslation('pronunciation', 'bn', false);
                        $savedHi = $wordSetList->getTranslation('pronunciation', 'hi', false);
                        $debugInfo[] = "Saved Bengali pronunciation: {$savedBn}";
                        $debugInfo[] = "Saved Hindi pronunciation: {$savedHi}";
                    }
                }

                // Process translations
                if (isset($wordData['translations']) && is_array($wordData['translations'])) {
                    foreach ($wordData['translations'] as $translationData) {
                        $locale = $translationData['locale'] ?? null;
                        if (!$locale || !in_array($locale, ['bn', 'hi'])) {
                            if (is_array($debugInfo)) {
                                $debugInfo[] = "Skipping translation: Invalid or unsupported locale '$locale'";
                            }
                            continue;
                        }

                        // Prepare translation data
                        $wordTranslation = $translationData['word_translation'] ?? '';
                        $sentenceTranslation = $translationData['example_sentence_translation'] ?? '';
                        $expressionTranslation = $translationData['example_expression_translation'] ?? '';

                        // Only save if at least one of the translation fields has content
                        if (!empty($wordTranslation) || !empty($sentenceTranslation) || !empty($expressionTranslation)) {
                            $translationSaveData = [
                                'word_translation' => $wordTranslation,
                                'word_transliteration' => $translationData['word_transliteration'] ?? null,
                                'example_sentence_translation' => $sentenceTranslation,
                                'example_sentence_transliteration' => $translationData['example_sentence_transliteration'] ?? null,
                                'example_expression_translation' => $expressionTranslation,
                                'example_expression_transliteration' => $translationData['example_expression_transliteration'] ?? null,
                                'source' => $translationData['source'] ?? 'oxford',
                                'article_word_set_list_id' => $wordSetList->id // Ensure this is explicitly set
                            ];

                            $translation = ArticleWordTranslation::updateOrCreate(
                                [
                                    'article_word_set_list_id' => $wordSetList->id,
                                    'locale' => $locale
                                ],
                                $translationSaveData
                            );

                            if (is_array($debugInfo)) {
                                $debugInfo[] = "Updated/Created Translation ID: {$translation->id} for word_set_list_id: {$translation->article_word_set_list_id}, locale: $locale";
                            }
                        } else {
                            if (is_array($debugInfo)) {
                                $debugInfo[] = "Skipping empty translation for locale: $locale";
                            }
                        }
                    }
                }
            }

            // Get all existing word list IDs for this set
            $existingListIds = ArticleWordSetList::where('article_word_set_id', $wordSet->id)
                ->pluck('id')
                ->toArray();

            // Delete any word lists that weren't in the JSON data
            $toDelete = array_diff($existingListIds, $processedIds);
            if (!empty($toDelete)) {
                ArticleWordSetList::whereIn('id', $toDelete)->delete();
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Deleted " . count($toDelete) . " word lists that were not in the JSON data";
                }
            }
        } else {
            if (is_array($debugInfo)) {
                $debugInfo[] = "No word_set_lists data found in JSON";
            }
        }

        return $wordSet;
    }

    /**
     * Generate initial JSON structure for creating a new word set
     *
     * @param int|null $articleId Optional article ID to associate with the new word set
     * @return string
     */
    public static function getInitialJson($articleId = null)
    {
        $initialData = [
            'id' => null,
            'article_id' => $articleId,
            'title' => '',
            'content' => '',
            'display_order' => 1,
            'static_content_1' => '',
            'static_content_2' => '',
            'title_translation' => [
                'bn' => '',
                'hi' => ''
            ],
            'content_translation' => [
                'bn' => '',
                'hi' => ''
            ],
            'column_order' => ['word', 'phonetic', 'meaning', 'example_sentence'],
            'word_set_lists' => [
                [
                    'id' => null,
                    'word' => '',
                    'phonetic' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'parts_of_speech' => '',
                    'static_content_1' => '',
                    'static_content_2' => '',
                    'meaning' => '',
                    'example_sentence' => '',
                    'example_expression' => '',
                    'example_expression_meaning' => '',
                    'display_order' => 1,
                    'synonyms' => '',
                    'antonyms' => '',
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'word_translation' => '',
                            'word_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'example_expression_translation' => '',
                            'example_expression_transliteration' => '',
                            'source' => 'oxford'
                        ],
                        [
                            'locale' => 'hi',
                            'word_translation' => '',
                            'word_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'example_expression_translation' => '',
                            'example_expression_transliteration' => '',
                            'source' => 'oxford'
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
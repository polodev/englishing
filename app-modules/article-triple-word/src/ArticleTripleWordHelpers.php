<?php

namespace Modules\ArticleTripleWord;

use Illuminate\Support\Str;
use Modules\ArticleTripleWord\Models\ArticleTripleWordSet;
use Modules\ArticleTripleWord\Models\ArticleTripleWordSetList;
use Modules\ArticleTripleWord\Models\ArticleTripleWordTranslation;

class ArticleTripleWordHelpers
{
    /**
     * Generate JSON representation of the triple word set list items
     *
     * @param ArticleTripleWordSet $tripleWordSet
     * @param array $supportedLocales The supported locales for translations (default: ['bn', 'hi'])
     * @return array
     */
    public static function generateJsonData(ArticleTripleWordSet $tripleWordSet, array $supportedLocales = ['bn', 'hi'])
    {
        // Build the root structure with word set properties
        $data = [
            'id' => $tripleWordSet->id,
            'article_id' => $tripleWordSet->article_id,
            'title' => $tripleWordSet->title,
            'content' => $tripleWordSet->content,
            'display_order' => $tripleWordSet->display_order,
            'title_translation' => [
                'bn' => $tripleWordSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $tripleWordSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $tripleWordSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $tripleWordSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($tripleWordSet->column_order) ?
                json_decode($tripleWordSet->column_order, true) :
                ($tripleWordSet->column_order ?? ['word_one', 'word_two', 'word_three']),
            'triple_word_set_lists' => []
        ];

        $tripleWordLists = ArticleTripleWordSetList::where('article_triple_word_set_id', $tripleWordSet->id)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($tripleWordLists->isNotEmpty()) {
            foreach ($tripleWordLists as $tripleWordList) {
                $translations = $tripleWordList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'word_one_translation' => $translation->word_one_translation ?? '',
                        'word_one_transliteration' => $translation->word_one_transliteration ?? '',
                        'word_two_translation' => $translation->word_two_translation ?? '',
                        'word_two_transliteration' => $translation->word_two_transliteration ?? '',
                        'word_three_translation' => $translation->word_three_translation ?? '',
                        'word_three_transliteration' => $translation->word_three_transliteration ?? '',
                        'example_sentence_translation' => $translation->example_sentence_translation ?? '',
                        'example_sentence_transliteration' => $translation->example_sentence_transliteration ?? '',
                    ];
                }

                // Get pronunciation data using Spatie's getTranslation method
                $pronunciation = [
                    'bn' => $tripleWordList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $tripleWordList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                $data['triple_word_set_lists'][] = [
                    'id' => $tripleWordList->id,
                    'word_one' => $tripleWordList->word_one,
                    'word_two' => $tripleWordList->word_two,
                    'word_three' => $tripleWordList->word_three,
                    'slug' => $tripleWordList->slug,
                    'pronunciation' => $pronunciation,
                    'parts_of_speech' => $tripleWordList->parts_of_speech,
                    'meaning' => $tripleWordList->meaning,
                    'example_sentence' => $tripleWordList->example_sentence,
                    'display_order' => $tripleWordList->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['triple_word_set_lists'] = [
                [
                    'word_one' => '',
                    'word_two' => '',
                    'word_three' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'parts_of_speech' => '',
                    'meaning' => '',
                    'example_sentence' => '',
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'word_one_translation' => '',
                            'word_one_transliteration' => '',
                            'word_two_translation' => '',
                            'word_two_transliteration' => '',
                            'word_three_translation' => '',
                            'word_three_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'word_one_translation' => '',
                            'word_one_transliteration' => '',
                            'word_two_translation' => '',
                            'word_two_transliteration' => '',
                            'word_three_translation' => '',
                            'word_three_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ]
                    ]
                ]
            ];
        }

        return $data;
    }

    /**
     * Process JSON data to update or create triple word list entries
     *
     * @param ArticleTripleWordSet|null $tripleWordSet Existing triple word set or null to create a new one
     * @param string $jsonData
     * @param array $debugInfo Optional array to store debug information
     * @return ArticleTripleWordSet The processed triple word set instance (existing or newly created)
     * @throws \Exception If there are issues processing the data
     */
    public static function processJsonData(?ArticleTripleWordSet $tripleWordSet, $jsonData, array &$debugInfo = [])
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
            throw new \Exception("JSON must be an object with triple word set data");
        }

        // If tripleWordSet is null, create a new ArticleTripleWordSet instance
        $creatingNew = false;
        if ($tripleWordSet === null) {
            $creatingNew = true;
            $tripleWordSet = new ArticleTripleWordSet();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Creating new ArticleTripleWordSet instance";
            }
        } else {
            // Debug info for existing triple word set
            if (is_array($debugInfo)) {
                $debugInfo[] = "Using article_triple_word_set_id: {$tripleWordSet->id}";
            }
        }

        // Verify the data has the expected structure
        if (!isset($data['triple_word_set_lists']) || !is_array($data['triple_word_set_lists'])) {
            throw new \Exception("Missing or invalid 'triple_word_set_lists' array in JSON data");
        }

        // 1. Update or create the Article Triple Word Set (parent) data
        if (isset($data['title']) || $creatingNew) {
            // Process column_order - ensure it's stored as a JSON string in the database
            $columnOrder = null;
            if (isset($data['column_order'])) {
                // Ensure column_order is saved as a JSON string, not an array
                $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
            }

            // Prepare triple word set data
            $tripleWordSetData = [
                'title' => $data['title'] ?? ($creatingNew ? '' : $tripleWordSet->title),
                'content' => $data['content'] ?? ($creatingNew ? '' : $tripleWordSet->content),
                'display_order' => $data['display_order'] ?? ($creatingNew ? 1 : $tripleWordSet->display_order),
                'column_order' => $columnOrder,
            ];
            
            // Add article_id only when creating new
            if ($creatingNew && isset($data['article_id'])) {
                $tripleWordSetData['article_id'] = $data['article_id'];
            }
            
            if ($creatingNew) {
                // Set all fields on the new model
                foreach ($tripleWordSetData as $key => $value) {
                    $tripleWordSet->$key = $value;
                }
                // Save the new triple word set
                $tripleWordSet->save();
            } else {
                // Update the existing triple word set
                $tripleWordSet->update($tripleWordSetData);
            }

            if (is_array($debugInfo)) {
                $debugInfo[] = "Updated ArticleTripleWordSet ID: {$tripleWordSet->id}";
            }

            // Handle title_translation directly for bn and hi
            if (isset($data['title_translation'])) {
                // Set Bengali title translation
                if (isset($data['title_translation']['bn'])) {
                    $tripleWordSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                }

                // Set Hindi title translation
                if (isset($data['title_translation']['hi'])) {
                    $tripleWordSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
            }

            // Handle content_translation directly for bn and hi
            if (isset($data['content_translation'])) {
                // Set Bengali content translation
                if (isset($data['content_translation']['bn'])) {
                    $tripleWordSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                }

                // Set Hindi content translation
                if (isset($data['content_translation']['hi'])) {
                    $tripleWordSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
            }

            // Save translations
            $tripleWordSet->save();
        }

        // Initialize arrays to track processed IDs
        $processedIds = [];
        $savedTranslationIds = [];
        $displayOrder = 1;

        // 2. Process each triple word in the array
        // Filter out items with empty words
        $validItems = [];
        foreach ($data['triple_word_set_lists'] as $index => $item) {
            if (!empty($item['word_one']) && !empty($item['word_two']) && !empty($item['word_three'])) {
                $validItems[] = $item;
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Skipped item #" . ($index + 1) . " with empty word_one, word_two, or word_three";
                }
            }
        }

        // Process only valid items
        foreach ($validItems as $index => $item) {
            // Create or update triple word set list using slug as the primary lookup key
            $slug = $item['slug'] ?? Str::slug($item['word_one'] . '-' . $item['word_two'] . '-' . $item['word_three']);

            // Try to find by slug first if we have a saved tripleWordSet
            $tripleWordList = null;
            if ($tripleWordSet->exists) {
                $tripleWordList = ArticleTripleWordSetList::where('slug', $slug)
                    ->where('article_triple_word_set_id', $tripleWordSet->id)
                    ->first();
            }

            if (!$tripleWordList) {
                // Create new record if not found by slug
                $tripleWordList = new ArticleTripleWordSetList();
                $tripleWordList->article_triple_word_set_id = $tripleWordSet->id;
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Creating new triple word list item with slug: {$slug}";
                }
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Updating existing triple word list item with slug: {$slug}";
                }
            }

            $tripleWordList->word_one = $item['word_one'];
            $tripleWordList->word_two = $item['word_two'];
            $tripleWordList->word_three = $item['word_three'];
            $tripleWordList->parts_of_speech = $item['parts_of_speech'] ?? '';
            $tripleWordList->meaning = $item['meaning'] ?? '';
            $tripleWordList->example_sentence = $item['example_sentence'] ?? '';
            $tripleWordList->display_order = $item['display_order'] ?? $displayOrder++;

            // Generate a unique slug to avoid duplicates
            $baseSlug = Str::slug($item['word_one'] . '-' . $item['word_two'] . '-' . $item['word_three']) ?: Str::uuid();
            $slug = $baseSlug;
            $counter = 1;

            // Check if this is a new record or we're changing the slug
            if (!$tripleWordList->exists || $tripleWordList->slug !== $baseSlug) {
                // Check for duplicate slugs and make unique if needed
                while (ArticleTripleWordSetList::where('article_triple_word_set_id', $tripleWordSet->id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $tripleWordList->id ?? 0)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
            }

            $tripleWordList->slug = $slug;

            // Handle pronunciation using Spatie's translatable functionality
            if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                // Handle Bengali pronunciation
                if (isset($item['pronunciation']['bn'])) {
                    $tripleWordList->setTranslation('pronunciation', 'bn', $item['pronunciation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali pronunciation for triple word: {$item['pronunciation']['bn']}";
                    }
                }

                // Handle Hindi pronunciation
                if (isset($item['pronunciation']['hi'])) {
                    $tripleWordList->setTranslation('pronunciation', 'hi', $item['pronunciation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi pronunciation for triple word: {$item['pronunciation']['hi']}";
                    }
                }
            } else {
                // Initialize empty pronunciations if not provided
                $tripleWordList->setTranslation('pronunciation', 'bn', '');
                $tripleWordList->setTranslation('pronunciation', 'hi', '');
            }

            $tripleWordList->save();

            $processedIds[] = $tripleWordList->id;

            // Process translations - only handle sequential format
            if (isset($item['translations']) && is_array($item['translations'])) {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Processing translations for triple word: {$item['word_one']} {$item['word_two']} {$item['word_three']}";
                }

                // Process translations in sequential array format (array of objects with locale key)
                foreach ($item['translations'] as $trData) {
                    if (!isset($trData['locale']) || !in_array($trData['locale'], ['bn', 'hi'])) {
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Skipping translation with invalid locale: " . ($trData['locale'] ?? 'unknown');
                        }
                        continue;
                    }

                    $locale = $trData['locale'];
                    $tr = ArticleTripleWordTranslation::where('article_triple_word_set_list_id', $tripleWordList->id)
                        ->where('locale', $locale)
                        ->first();

                    if (!$tr) {
                        $tr = new ArticleTripleWordTranslation();
                        $tr->article_triple_word_set_list_id = $tripleWordList->id;
                        $tr->locale = $locale;
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Created new translation for locale: {$locale}";
                        }
                    } else {
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Updating existing translation for locale: {$locale}";
                        }
                    }

                    $tr->word_one_translation = $trData['word_one_translation'] ?? '';
                    $tr->word_one_transliteration = $trData['word_one_transliteration'] ?? '';
                    $tr->word_two_translation = $trData['word_two_translation'] ?? '';
                    $tr->word_two_transliteration = $trData['word_two_transliteration'] ?? '';
                    $tr->word_three_translation = $trData['word_three_translation'] ?? '';
                    $tr->word_three_transliteration = $trData['word_three_transliteration'] ?? '';
                    $tr->example_sentence_translation = $trData['example_sentence_translation'] ?? '';
                    $tr->example_sentence_transliteration = $trData['example_sentence_transliteration'] ?? '';
                    $tr->save();
                    $savedTranslationIds[] = $tr->id;
                }
            }
        }

        // Delete missing translations
        if ($tripleWordSet->exists && !empty($processedIds)) {
            ArticleTripleWordTranslation::whereIn('article_triple_word_set_list_id', $processedIds)
                ->whereNotIn('id', $savedTranslationIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted translations that are no longer present in the input";
            }

            // Delete records not in the processed list
            $deletedCount = ArticleTripleWordSetList::where('article_triple_word_set_id', $tripleWordSet->id)
                ->whereNotIn('id', $processedIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted {$deletedCount} triple word list items that are no longer present in the input";
            }
        }

        return $tripleWordSet;
    }

    /**
     * Generate initial JSON structure for creating a new triple word set
     *
     * @param int|null $articleId Optional article ID to associate with the new triple word set
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
            'title_translation' => [
                'bn' => '',
                'hi' => ''
            ],
            'content_translation' => [
                'bn' => '',
                'hi' => ''
            ],
            'column_order' => ['word_one', 'word_two', 'word_three', 'meaning', 'example_sentence'],
            'triple_word_set_lists' => [
                [
                    'word_one' => '',
                    'word_two' => '',
                    'word_three' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'parts_of_speech' => '',
                    'meaning' => '',
                    'example_sentence' => '',
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'word_one_translation' => '',
                            'word_one_transliteration' => '',
                            'word_two_translation' => '',
                            'word_two_transliteration' => '',
                            'word_three_translation' => '',
                            'word_three_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'word_one_translation' => '',
                            'word_one_transliteration' => '',
                            'word_two_translation' => '',
                            'word_two_transliteration' => '',
                            'word_three_translation' => '',
                            'word_three_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
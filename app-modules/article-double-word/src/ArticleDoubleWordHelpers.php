<?php

namespace Modules\ArticleDoubleWord;

use Illuminate\Support\Str;
use Modules\ArticleDoubleWord\Models\ArticleDoubleWordSet;
use Modules\ArticleDoubleWord\Models\ArticleDoubleWordSetList;
use Modules\ArticleDoubleWord\Models\ArticleDoubleWordTranslation;

class ArticleDoubleWordHelpers
{
    /**
     * Generate JSON representation of the double word set list items
     *
     * @param ArticleDoubleWordSet $doubleWordSet
     * @param array $supportedLocales The supported locales for translations (default: ['bn', 'hi'])
     * @return array
     */
    public static function generateJsonData(ArticleDoubleWordSet $doubleWordSet, array $supportedLocales = ['bn', 'hi'])
    {
        // Build the root structure with word set properties
        $data = [
            'id' => $doubleWordSet->id,
            'article_id' => $doubleWordSet->article_id,
            'title' => $doubleWordSet->title,
            'content' => $doubleWordSet->content,
            'display_order' => $doubleWordSet->display_order,
            'title_translation' => [
                'bn' => $doubleWordSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $doubleWordSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $doubleWordSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $doubleWordSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($doubleWordSet->column_order) ?
                json_decode($doubleWordSet->column_order, true) :
                ($doubleWordSet->column_order ?? ['word_one', 'word_two']),
            'double_word_set_lists' => []
        ];

        $doubleWordLists = ArticleDoubleWordSetList::where('article_double_word_set_id', $doubleWordSet->id)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($doubleWordLists->isNotEmpty()) {
            foreach ($doubleWordLists as $doubleWordList) {
                $translations = $doubleWordList->translations->keyBy('locale');
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
                        'example_sentence_translation' => $translation->example_sentence_translation ?? '',
                        'example_sentence_transliteration' => $translation->example_sentence_transliteration ?? '',
                    ];
                }

                // Get pronunciation data using Spatie's getTranslation method
                $pronunciation = [
                    'bn' => $doubleWordList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $doubleWordList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                $data['double_word_set_lists'][] = [
                    'id' => $doubleWordList->id,
                    'word_one' => $doubleWordList->word_one,
                    'word_two' => $doubleWordList->word_two,
                    'slug' => $doubleWordList->slug,
                    'pronunciation' => $pronunciation,
                    'parts_of_speech' => $doubleWordList->parts_of_speech,
                    'meaning' => $doubleWordList->meaning,
                    'example_sentence' => $doubleWordList->example_sentence,
                    'display_order' => $doubleWordList->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['double_word_set_lists'] = [
                [
                    'word_one' => '',
                    'word_two' => '',
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
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'word_one_translation' => '',
                            'word_one_transliteration' => '',
                            'word_two_translation' => '',
                            'word_two_transliteration' => '',
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
     * Process JSON data to update or create double word list entries
     *
     * @param ArticleDoubleWordSet|null $doubleWordSet Existing double word set or null to create a new one
     * @param string $jsonData
     * @param array $debugInfo Optional array to store debug information
     * @return ArticleDoubleWordSet The processed double word set instance (existing or newly created)
     * @throws \Exception If there are issues processing the data
     */
    public static function processJsonData(?ArticleDoubleWordSet $doubleWordSet, $jsonData, array &$debugInfo = [])
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
            throw new \Exception("JSON must be an object with double word set data");
        }

        // If doubleWordSet is null, create a new ArticleDoubleWordSet instance
        $creatingNew = false;
        if ($doubleWordSet === null) {
            $creatingNew = true;
            $doubleWordSet = new ArticleDoubleWordSet();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Creating new ArticleDoubleWordSet instance";
            }
        } else {
            // Debug info for existing double word set
            if (is_array($debugInfo)) {
                $debugInfo[] = "Using article_double_word_set_id: {$doubleWordSet->id}";
            }
        }

        // Verify the data has the expected structure
        if (!isset($data['double_word_set_lists']) || !is_array($data['double_word_set_lists'])) {
            throw new \Exception("Missing or invalid 'double_word_set_lists' array in JSON data");
        }

        // 1. Update or create the Article Double Word Set (parent) data
        if (isset($data['title']) || $creatingNew) {
            // Process column_order - ensure it's stored as a JSON string in the database
            $columnOrder = null;
            if (isset($data['column_order'])) {
                // Ensure column_order is saved as a JSON string, not an array
                $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
            }

            // Prepare double word set data
            $doubleWordSetData = [
                'title' => $data['title'] ?? ($creatingNew ? '' : $doubleWordSet->title),
                'content' => $data['content'] ?? ($creatingNew ? '' : $doubleWordSet->content),
                'display_order' => $data['display_order'] ?? ($creatingNew ? 1 : $doubleWordSet->display_order),
                'column_order' => $columnOrder,
            ];
            
            // Add article_id only when creating new
            if ($creatingNew && isset($data['article_id'])) {
                $doubleWordSetData['article_id'] = $data['article_id'];
            }
            
            if ($creatingNew) {
                // Set all fields on the new model
                foreach ($doubleWordSetData as $key => $value) {
                    $doubleWordSet->$key = $value;
                }
                // Save the new double word set
                $doubleWordSet->save();
            } else {
                // Update the existing double word set
                $doubleWordSet->update($doubleWordSetData);
            }

            if (is_array($debugInfo)) {
                $debugInfo[] = "Updated ArticleDoubleWordSet ID: {$doubleWordSet->id}";
            }

            // Handle title_translation directly for bn and hi
            if (isset($data['title_translation'])) {
                // Set Bengali title translation
                if (isset($data['title_translation']['bn'])) {
                    $doubleWordSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                }

                // Set Hindi title translation
                if (isset($data['title_translation']['hi'])) {
                    $doubleWordSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
            }

            // Handle content_translation directly for bn and hi
            if (isset($data['content_translation'])) {
                // Set Bengali content translation
                if (isset($data['content_translation']['bn'])) {
                    $doubleWordSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                }

                // Set Hindi content translation
                if (isset($data['content_translation']['hi'])) {
                    $doubleWordSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
            }

            // Save translations
            $doubleWordSet->save();
        }

        // Initialize arrays to track processed IDs
        $processedIds = [];
        $savedTranslationIds = [];
        $displayOrder = 1;

        // 2. Process each double word in the array
        // Filter out items with empty words
        $validItems = [];
        foreach ($data['double_word_set_lists'] as $index => $item) {
            if (!empty($item['word_one']) && !empty($item['word_two'])) {
                $validItems[] = $item;
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Skipped item #" . ($index + 1) . " with empty word_one or word_two";
                }
            }
        }

        // Process only valid items
        foreach ($validItems as $index => $item) {
            // Create or update double word set list using slug as the primary lookup key
            $slug = $item['slug'] ?? Str::slug($item['word_one'] . '-' . $item['word_two']);

            // Try to find by slug first if we have a saved doubleWordSet
            $doubleWordList = null;
            if ($doubleWordSet->exists) {
                $doubleWordList = ArticleDoubleWordSetList::where('slug', $slug)
                    ->where('article_double_word_set_id', $doubleWordSet->id)
                    ->first();
            }

            if (!$doubleWordList) {
                // Create new record if not found by slug
                $doubleWordList = new ArticleDoubleWordSetList();
                $doubleWordList->article_double_word_set_id = $doubleWordSet->id;
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Creating new double word list item with slug: {$slug}";
                }
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Updating existing double word list item with slug: {$slug}";
                }
            }

            $doubleWordList->word_one = $item['word_one'];
            $doubleWordList->word_two = $item['word_two'];
            $doubleWordList->parts_of_speech = $item['parts_of_speech'] ?? '';
            $doubleWordList->meaning = $item['meaning'] ?? '';
            $doubleWordList->example_sentence = $item['example_sentence'] ?? '';
            $doubleWordList->display_order = $item['display_order'] ?? $displayOrder++;

            // Generate a unique slug to avoid duplicates
            $baseSlug = Str::slug($item['word_one'] . '-' . $item['word_two']) ?: Str::uuid();
            $slug = $baseSlug;
            $counter = 1;

            // Check if this is a new record or we're changing the slug
            if (!$doubleWordList->exists || $doubleWordList->slug !== $baseSlug) {
                // Check for duplicate slugs and make unique if needed
                while (ArticleDoubleWordSetList::where('article_double_word_set_id', $doubleWordSet->id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $doubleWordList->id ?? 0)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
            }

            $doubleWordList->slug = $slug;

            // Handle pronunciation using Spatie's translatable functionality
            if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                // Handle Bengali pronunciation
                if (isset($item['pronunciation']['bn'])) {
                    $doubleWordList->setTranslation('pronunciation', 'bn', $item['pronunciation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali pronunciation for double word: {$item['pronunciation']['bn']}";
                    }
                }

                // Handle Hindi pronunciation
                if (isset($item['pronunciation']['hi'])) {
                    $doubleWordList->setTranslation('pronunciation', 'hi', $item['pronunciation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi pronunciation for double word: {$item['pronunciation']['hi']}";
                    }
                }
            } else {
                // Initialize empty pronunciations if not provided
                $doubleWordList->setTranslation('pronunciation', 'bn', '');
                $doubleWordList->setTranslation('pronunciation', 'hi', '');
            }

            $doubleWordList->save();

            $processedIds[] = $doubleWordList->id;

            // Process translations - only handle sequential format
            if (isset($item['translations']) && is_array($item['translations'])) {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Processing translations for double word: {$item['word_one']} {$item['word_two']}";
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
                    $tr = ArticleDoubleWordTranslation::where('article_double_word_set_list_id', $doubleWordList->id)
                        ->where('locale', $locale)
                        ->first();

                    if (!$tr) {
                        $tr = new ArticleDoubleWordTranslation();
                        $tr->article_double_word_set_list_id = $doubleWordList->id;
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
                    $tr->example_sentence_translation = $trData['example_sentence_translation'] ?? '';
                    $tr->example_sentence_transliteration = $trData['example_sentence_transliteration'] ?? '';
                    $tr->save();
                    $savedTranslationIds[] = $tr->id;
                }
            }
        }

        // Delete missing translations
        if ($doubleWordSet->exists && !empty($processedIds)) {
            ArticleDoubleWordTranslation::whereIn('article_double_word_set_list_id', $processedIds)
                ->whereNotIn('id', $savedTranslationIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted translations that are no longer present in the input";
            }

            // Delete records not in the processed list
            $deletedCount = ArticleDoubleWordSetList::where('article_double_word_set_id', $doubleWordSet->id)
                ->whereNotIn('id', $processedIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted {$deletedCount} double word list items that are no longer present in the input";
            }
        }

        return $doubleWordSet;
    }

    /**
     * Generate initial JSON structure for creating a new double word set
     *
     * @param int|null $articleId Optional article ID to associate with the new double word set
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
            'column_order' => ['word_one', 'word_two', 'meaning', 'example_sentence'],
            'double_word_set_lists' => [
                [
                    'word_one' => '',
                    'word_two' => '',
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
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'word_one_translation' => '',
                            'word_one_transliteration' => '',
                            'word_two_translation' => '',
                            'word_two_transliteration' => '',
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
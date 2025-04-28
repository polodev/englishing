<?php

namespace Modules\ArticleDoubleSentence;

use Illuminate\Support\Str;
use Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceSet;
use Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceSetList;
use Modules\ArticleDoubleSentence\Models\ArticleDoubleSentenceTranslation;

class ArticleDoubleSentenceHelpers
{
    /**
     * Generate JSON representation of the double sentence set list items
     *
     * @param ArticleDoubleSentenceSet $doubleSentenceSet
     * @param array $supportedLocales The supported locales for translations (default: ['bn', 'hi'])
     * @return array
     */
    public static function generateJsonData(ArticleDoubleSentenceSet $doubleSentenceSet, array $supportedLocales = ['bn', 'hi'])
    {
        // Build the root structure with sentence set properties
        $data = [
            'id' => $doubleSentenceSet->id,
            'article_id' => $doubleSentenceSet->article_id,
            'title' => $doubleSentenceSet->title,
            'content' => $doubleSentenceSet->content,
            'display_order' => $doubleSentenceSet->display_order,
            'title_translation' => [
                'bn' => $doubleSentenceSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $doubleSentenceSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $doubleSentenceSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $doubleSentenceSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($doubleSentenceSet->column_order) ?
                json_decode($doubleSentenceSet->column_order, true) :
                ($doubleSentenceSet->column_order ?? ['sentence_one', 'sentence_two']),
            'double_sentence_set_lists' => []
        ];

        $doubleSentenceLists = ArticleDoubleSentenceSetList::where('article_double_sentence_set_id', $doubleSentenceSet->id)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($doubleSentenceLists->isNotEmpty()) {
            foreach ($doubleSentenceLists as $doubleSentenceList) {
                $translations = $doubleSentenceList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'sentence_one_translation' => $translation->sentence_one_translation ?? '',
                        'sentence_one_transliteration' => $translation->sentence_one_transliteration ?? '',
                        'sentence_two_translation' => $translation->sentence_two_translation ?? '',
                        'sentence_two_transliteration' => $translation->sentence_two_transliteration ?? '',
                    ];
                }

                // Get pronunciation data using Spatie's getTranslation method
                $pronunciation = [
                    'bn' => $doubleSentenceList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $doubleSentenceList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                $data['double_sentence_set_lists'][] = [
                    'id' => $doubleSentenceList->id,
                    'sentence_one' => $doubleSentenceList->sentence_one,
                    'sentence_two' => $doubleSentenceList->sentence_two,
                    'slug' => $doubleSentenceList->slug,
                    'pronunciation' => $pronunciation,
                    'display_order' => $doubleSentenceList->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['double_sentence_set_lists'] = [
                [
                    'sentence_one' => '',
                    'sentence_two' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'sentence_one_translation' => '',
                            'sentence_one_transliteration' => '',
                            'sentence_two_translation' => '',
                            'sentence_two_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'sentence_one_translation' => '',
                            'sentence_one_transliteration' => '',
                            'sentence_two_translation' => '',
                            'sentence_two_transliteration' => '',
                        ]
                    ]
                ]
            ];
        }

        return $data;
    }

    /**
     * Process JSON data to update or create double sentence list entries
     *
     * @param ArticleDoubleSentenceSet|null $doubleSentenceSet Existing double sentence set or null to create a new one
     * @param string $jsonData
     * @param array $debugInfo Optional array to store debug information
     * @return ArticleDoubleSentenceSet The processed double sentence set instance (existing or newly created)
     * @throws \Exception If there are issues processing the data
     */
    public static function processJsonData(?ArticleDoubleSentenceSet $doubleSentenceSet, $jsonData, array &$debugInfo = [])
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
            throw new \Exception("JSON must be an object with double sentence set data");
        }

        // If doubleSentenceSet is null, create a new ArticleDoubleSentenceSet instance
        $creatingNew = false;
        if ($doubleSentenceSet === null) {
            $creatingNew = true;
            $doubleSentenceSet = new ArticleDoubleSentenceSet();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Creating new ArticleDoubleSentenceSet instance";
            }
        } else {
            // Debug info for existing double sentence set
            if (is_array($debugInfo)) {
                $debugInfo[] = "Using article_double_sentence_set_id: {$doubleSentenceSet->id}";
            }
        }

        // Verify the data has the expected structure
        if (!isset($data['double_sentence_set_lists']) || !is_array($data['double_sentence_set_lists'])) {
            throw new \Exception("Missing or invalid 'double_sentence_set_lists' array in JSON data");
        }

        // 1. Update or create the Article Double Sentence Set (parent) data
        if (isset($data['title']) || $creatingNew) {
            // Process column_order - ensure it's stored as a JSON string in the database
            $columnOrder = null;
            if (isset($data['column_order'])) {
                // Ensure column_order is saved as a JSON string, not an array
                $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
            }

            // Prepare double sentence set data
            $doubleSentenceSetData = [
                'title' => $data['title'] ?? ($creatingNew ? '' : $doubleSentenceSet->title),
                'content' => $data['content'] ?? ($creatingNew ? '' : $doubleSentenceSet->content),
                'display_order' => $data['display_order'] ?? ($creatingNew ? 1 : $doubleSentenceSet->display_order),
                'column_order' => $columnOrder,
            ];
            
            // Add article_id only when creating new
            if ($creatingNew && isset($data['article_id'])) {
                $doubleSentenceSetData['article_id'] = $data['article_id'];
            }
            
            if ($creatingNew) {
                // Set all fields on the new model
                foreach ($doubleSentenceSetData as $key => $value) {
                    $doubleSentenceSet->$key = $value;
                }
                // Save the new double sentence set
                $doubleSentenceSet->save();
            } else {
                // Update the existing double sentence set
                $doubleSentenceSet->update($doubleSentenceSetData);
            }

            if (is_array($debugInfo)) {
                $debugInfo[] = "Updated ArticleDoubleSentenceSet ID: {$doubleSentenceSet->id}";
            }

            // Handle title_translation directly for bn and hi
            if (isset($data['title_translation'])) {
                // Set Bengali title translation
                if (isset($data['title_translation']['bn'])) {
                    $doubleSentenceSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                }

                // Set Hindi title translation
                if (isset($data['title_translation']['hi'])) {
                    $doubleSentenceSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
            }

            // Handle content_translation directly for bn and hi
            if (isset($data['content_translation'])) {
                // Set Bengali content translation
                if (isset($data['content_translation']['bn'])) {
                    $doubleSentenceSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                }

                // Set Hindi content translation
                if (isset($data['content_translation']['hi'])) {
                    $doubleSentenceSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
            }

            // Save translations
            $doubleSentenceSet->save();
        }

        // Initialize arrays to track processed IDs
        $processedIds = [];
        $savedTranslationIds = [];
        $displayOrder = 1;

        // 2. Process each double sentence in the array
        // Filter out items with empty sentences
        $validItems = [];
        foreach ($data['double_sentence_set_lists'] as $index => $item) {
            if (!empty($item['sentence_one']) && !empty($item['sentence_two'])) {
                $validItems[] = $item;
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Skipped item #" . ($index + 1) . " with empty sentence_one or sentence_two";
                }
            }
        }

        // Process only valid items
        foreach ($validItems as $index => $item) {
            // Create or update double sentence set list using slug as the primary lookup key
            $slug = $item['slug'] ?? Str::slug($item['sentence_one'] . '-' . $item['sentence_two']);

            // Try to find by slug first if we have a saved doubleSentenceSet
            $doubleSentenceList = null;
            if ($doubleSentenceSet->exists) {
                $doubleSentenceList = ArticleDoubleSentenceSetList::where('slug', $slug)
                    ->where('article_double_sentence_set_id', $doubleSentenceSet->id)
                    ->first();
            }

            if (!$doubleSentenceList) {
                // Create new record if not found by slug
                $doubleSentenceList = new ArticleDoubleSentenceSetList();
                $doubleSentenceList->article_double_sentence_set_id = $doubleSentenceSet->id;
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Creating new double sentence list item with slug: {$slug}";
                }
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Updating existing double sentence list item with slug: {$slug}";
                }
            }

            $doubleSentenceList->sentence_one = $item['sentence_one'];
            $doubleSentenceList->sentence_two = $item['sentence_two'];
            $doubleSentenceList->display_order = $item['display_order'] ?? $displayOrder++;

            // Generate a unique slug to avoid duplicates
            $baseSlug = Str::slug($item['sentence_one'] . '-' . $item['sentence_two']) ?: Str::uuid();
            $slug = $baseSlug;
            $counter = 1;

            // Check if this is a new record or we're changing the slug
            if (!$doubleSentenceList->exists || $doubleSentenceList->slug !== $baseSlug) {
                // Check for duplicate slugs and make unique if needed
                while (ArticleDoubleSentenceSetList::where('article_double_sentence_set_id', $doubleSentenceSet->id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $doubleSentenceList->id ?? 0)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
            }

            $doubleSentenceList->slug = $slug;

            // Handle pronunciation using Spatie's translatable functionality
            if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                // Handle Bengali pronunciation
                if (isset($item['pronunciation']['bn'])) {
                    $doubleSentenceList->setTranslation('pronunciation', 'bn', $item['pronunciation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali pronunciation for double sentence: {$item['pronunciation']['bn']}";
                    }
                }

                // Handle Hindi pronunciation
                if (isset($item['pronunciation']['hi'])) {
                    $doubleSentenceList->setTranslation('pronunciation', 'hi', $item['pronunciation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi pronunciation for double sentence: {$item['pronunciation']['hi']}";
                    }
                }
            } else {
                // Initialize empty pronunciations if not provided
                $doubleSentenceList->setTranslation('pronunciation', 'bn', '');
                $doubleSentenceList->setTranslation('pronunciation', 'hi', '');
            }

            $doubleSentenceList->save();

            $processedIds[] = $doubleSentenceList->id;

            // Process translations - only handle sequential format
            if (isset($item['translations']) && is_array($item['translations'])) {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Processing translations for double sentence: {$item['sentence_one']} / {$item['sentence_two']}";
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
                    $tr = ArticleDoubleSentenceTranslation::where('article_double_sentence_set_list_id', $doubleSentenceList->id)
                        ->where('locale', $locale)
                        ->first();

                    if (!$tr) {
                        $tr = new ArticleDoubleSentenceTranslation();
                        $tr->article_double_sentence_set_list_id = $doubleSentenceList->id;
                        $tr->locale = $locale;
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Created new translation for locale: {$locale}";
                        }
                    } else {
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Updating existing translation for locale: {$locale}";
                        }
                    }

                    $tr->sentence_one_translation = $trData['sentence_one_translation'] ?? '';
                    $tr->sentence_one_transliteration = $trData['sentence_one_transliteration'] ?? '';
                    $tr->sentence_two_translation = $trData['sentence_two_translation'] ?? '';
                    $tr->sentence_two_transliteration = $trData['sentence_two_transliteration'] ?? '';
                    $tr->save();
                    $savedTranslationIds[] = $tr->id;
                }
            }
        }

        // Delete missing translations
        if ($doubleSentenceSet->exists && !empty($processedIds)) {
            ArticleDoubleSentenceTranslation::whereIn('article_double_sentence_set_list_id', $processedIds)
                ->whereNotIn('id', $savedTranslationIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted translations that are no longer present in the input";
            }

            // Delete records not in the processed list
            $deletedCount = ArticleDoubleSentenceSetList::where('article_double_sentence_set_id', $doubleSentenceSet->id)
                ->whereNotIn('id', $processedIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted {$deletedCount} double sentence list items that are no longer present in the input";
            }
        }

        return $doubleSentenceSet;
    }

    /**
     * Generate initial JSON structure for creating a new double sentence set
     *
     * @param int|null $articleId Optional article ID to associate with the new double sentence set
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
            'column_order' => ['sentence_one', 'sentence_two'],
            'double_sentence_set_lists' => [
                [
                    'sentence_one' => '',
                    'sentence_two' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'sentence_one_translation' => '',
                            'sentence_one_transliteration' => '',
                            'sentence_two_translation' => '',
                            'sentence_two_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'sentence_one_translation' => '',
                            'sentence_one_transliteration' => '',
                            'sentence_two_translation' => '',
                            'sentence_two_transliteration' => '',
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
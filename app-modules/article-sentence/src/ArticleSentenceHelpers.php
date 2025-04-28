<?php

namespace Modules\ArticleSentence;

use Illuminate\Support\Str;
use Modules\ArticleSentence\Models\ArticleSentenceSet;
use Modules\ArticleSentence\Models\ArticleSentenceSetList;
use Modules\ArticleSentence\Models\ArticleSentenceTranslation;

class ArticleSentenceHelpers
{
    /**
     * Generate JSON representation of the sentence set list items
     *
     * @param ArticleSentenceSet $sentenceSet
     * @param array $supportedLocales The supported locales for translations (default: ['bn', 'hi'])
     * @return array
     */
    public static function generateJsonData(ArticleSentenceSet $sentenceSet, array $supportedLocales = ['bn', 'hi'])
    {
        // Build the root structure with sentence set properties
        $data = [
            'id' => $sentenceSet->id,
            'article_id' => $sentenceSet->article_id,
            'title' => $sentenceSet->title,
            'content' => $sentenceSet->content,
            'display_order' => $sentenceSet->display_order,
            'title_translation' => [
                'bn' => $sentenceSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $sentenceSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $sentenceSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $sentenceSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($sentenceSet->column_order) ?
                json_decode($sentenceSet->column_order, true) :
                ($sentenceSet->column_order ?? ['sentence']),
            'sentence_set_lists' => []
        ];

        $sentenceLists = ArticleSentenceSetList::where('article_sentence_set_id', $sentenceSet->id)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($sentenceLists->isNotEmpty()) {
            foreach ($sentenceLists as $sentenceList) {
                $translations = $sentenceList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'translation' => $translation->translation ?? '',
                        'transliteration' => $translation->transliteration ?? '',
                    ];
                }

                // Get pronunciation data using correct methods for translatable fields
                $pronunciation = [
                    'bn' => $sentenceList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $sentenceList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                $data['sentence_set_lists'][] = [
                    'id' => $sentenceList->id,
                    'sentence' => $sentenceList->sentence,
                    'slug' => $sentenceList->slug,
                    'pronunciation' => $pronunciation,
                    'display_order' => $sentenceList->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['sentence_set_lists'] = [
                [
                    'sentence' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'translation' => '',
                            'transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'translation' => '',
                            'transliteration' => '',
                        ]
                    ]
                ]
            ];
        }

        return $data;
    }

    /**
     * Process JSON data to update or create sentence list entries
     *
     * @param ArticleSentenceSet|null $sentenceSet Existing sentence set or null to create a new one
     * @param string $jsonData
     * @param array $debugInfo Optional array to store debug information
     * @return ArticleSentenceSet The processed sentence set instance (existing or newly created)
     * @throws \Exception If there are issues processing the data
     */
    public static function processJsonData(?ArticleSentenceSet $sentenceSet, $jsonData, array &$debugInfo = [])
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
            throw new \Exception("JSON must be an object with sentence set data");
        }

        // If sentenceSet is null, create a new ArticleSentenceSet instance
        $creatingNew = false;
        if ($sentenceSet === null) {
            $creatingNew = true;
            $sentenceSet = new ArticleSentenceSet();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Creating new ArticleSentenceSet instance";
            }
        } else {
            // Debug info for existing sentence set
            if (is_array($debugInfo)) {
                $debugInfo[] = "Using article_sentence_set_id: {$sentenceSet->id}";
            }
        }

        // Verify the data has the expected structure
        if (!isset($data['sentence_set_lists']) || !is_array($data['sentence_set_lists'])) {
            throw new \Exception("Missing or invalid 'sentence_set_lists' array in JSON data");
        }

        // 1. Update or create the Article Sentence Set (parent) data
        if (isset($data['title']) || $creatingNew) {
            // Process column_order - ensure it's stored as a JSON string in the database
            $columnOrder = null;
            if (isset($data['column_order'])) {
                // Ensure column_order is saved as a JSON string, not an array
                $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
            }

            // Prepare sentence set data
            $sentenceSetData = [
                'title' => $data['title'] ?? ($creatingNew ? '' : $sentenceSet->title),
                'content' => $data['content'] ?? ($creatingNew ? '' : $sentenceSet->content),
                'display_order' => $data['display_order'] ?? ($creatingNew ? 1 : $sentenceSet->display_order),
                'column_order' => $columnOrder,
            ];
            
            // Add article_id only when creating new
            if ($creatingNew && isset($data['article_id'])) {
                $sentenceSetData['article_id'] = $data['article_id'];
            }
            
            if ($creatingNew) {
                // Set all fields on the new model
                foreach ($sentenceSetData as $key => $value) {
                    $sentenceSet->$key = $value;
                }
                // Save the new sentence set
                $sentenceSet->save();
            } else {
                // Update the existing sentence set
                $sentenceSet->update($sentenceSetData);
            }

            if (is_array($debugInfo)) {
                $debugInfo[] = "Updated ArticleSentenceSet ID: {$sentenceSet->id}";
            }

            // Handle title_translation directly for bn and hi
            if (isset($data['title_translation'])) {
                // Set Bengali title translation
                if (isset($data['title_translation']['bn'])) {
                    $sentenceSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                }

                // Set Hindi title translation
                if (isset($data['title_translation']['hi'])) {
                    $sentenceSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
            }

            // Handle content_translation directly for bn and hi
            if (isset($data['content_translation'])) {
                // Set Bengali content translation
                if (isset($data['content_translation']['bn'])) {
                    $sentenceSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                }

                // Set Hindi content translation
                if (isset($data['content_translation']['hi'])) {
                    $sentenceSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
            }

            // Save translations
            $sentenceSet->save();
        }

        // Initialize arrays to track processed IDs
        $processedIds = [];
        $savedTranslationIds = [];
        $displayOrder = 1;

        // 2. Process each sentence in the array
        // Filter out items with empty sentences
        $validItems = [];
        foreach ($data['sentence_set_lists'] as $index => $item) {
            if (!empty($item['sentence'])) {
                $validItems[] = $item;
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Skipped item #" . ($index + 1) . " with empty sentence";
                }
            }
        }

        // Process only valid items
        foreach ($validItems as $index => $item) {
            // Create or update sentence set list using slug as the primary lookup key
            $slug = $item['slug'] ?? Str::slug($item['sentence']);

            // Try to find by slug first if we have a saved sentenceSet
            $sentenceList = null;
            if ($sentenceSet->exists) {
                $sentenceList = ArticleSentenceSetList::where('slug', $slug)
                    ->where('article_sentence_set_id', $sentenceSet->id)
                    ->first();
            }

            if (!$sentenceList) {
                // Create new record if not found by slug
                $sentenceList = new ArticleSentenceSetList();
                $sentenceList->article_sentence_set_id = $sentenceSet->id;
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Creating new sentence list item with slug: {$slug}";
                }
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Updating existing sentence list item with slug: {$slug}";
                }
            }

            $sentenceList->sentence = $item['sentence'];
            $sentenceList->display_order = $item['display_order'] ?? $displayOrder++;

            // Generate a unique slug to avoid duplicates
            $baseSlug = Str::slug($item['sentence']) ?: Str::uuid();
            $slug = $baseSlug;
            $counter = 1;

            // Check if this is a new record or we're changing the slug
            if (!$sentenceList->exists || $sentenceList->slug !== $baseSlug) {
                // Check for duplicate slugs and make unique if needed
                while (ArticleSentenceSetList::where('article_sentence_set_id', $sentenceSet->id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $sentenceList->id ?? 0)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
            }

            $sentenceList->slug = $slug;

            // Handle pronunciation using Spatie's translatable functionality
            if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                // Handle Bengali pronunciation
                if (isset($item['pronunciation']['bn'])) {
                    $sentenceList->setTranslation('pronunciation', 'bn', $item['pronunciation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali pronunciation for sentence: {$item['pronunciation']['bn']}";
                    }
                }

                // Handle Hindi pronunciation
                if (isset($item['pronunciation']['hi'])) {
                    $sentenceList->setTranslation('pronunciation', 'hi', $item['pronunciation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi pronunciation for sentence: {$item['pronunciation']['hi']}";
                    }
                }
            } else {
                // Initialize empty pronunciations if not provided
                $sentenceList->setTranslation('pronunciation', 'bn', '');
                $sentenceList->setTranslation('pronunciation', 'hi', '');
            }

            $sentenceList->save();

            $processedIds[] = $sentenceList->id;

            // Process translations - only handle sequential format
            if (isset($item['translations']) && is_array($item['translations'])) {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Processing translations for sentence: {$item['sentence']}";
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
                    $tr = ArticleSentenceTranslation::where('article_sentence_set_list_id', $sentenceList->id)
                        ->where('locale', $locale)
                        ->first();

                    if (!$tr) {
                        $tr = new ArticleSentenceTranslation();
                        $tr->article_sentence_set_list_id = $sentenceList->id;
                        $tr->locale = $locale;
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Created new translation for locale: {$locale}";
                        }
                    } else {
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Updating existing translation for locale: {$locale}";
                        }
                    }

                    $tr->translation = $trData['translation'] ?? '';
                    $tr->transliteration = $trData['transliteration'] ?? '';
                    $tr->save();
                    $savedTranslationIds[] = $tr->id;
                }
            }
        }

        // Delete missing translations
        if ($sentenceSet->exists && !empty($processedIds)) {
            ArticleSentenceTranslation::whereIn('article_sentence_set_list_id', $processedIds)
                ->whereNotIn('id', $savedTranslationIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted translations that are no longer present in the input";
            }

            // Delete records not in the processed list
            $deletedCount = ArticleSentenceSetList::where('article_sentence_set_id', $sentenceSet->id)
                ->whereNotIn('id', $processedIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted {$deletedCount} sentence list items that are no longer present in the input";
            }
        }

        return $sentenceSet;
    }

    /**
     * Generate initial JSON structure for creating a new sentence set
     *
     * @param int|null $articleId Optional article ID to associate with the new sentence set
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
            'column_order' => ['sentence'],
            'sentence_set_lists' => [
                [
                    'sentence' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'translation' => '',
                            'transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'translation' => '',
                            'transliteration' => '',
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
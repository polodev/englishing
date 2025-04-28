<?php

namespace Modules\ArticleExpression;

use Illuminate\Support\Str;
use Modules\ArticleExpression\Models\ArticleExpressionSet;
use Modules\ArticleExpression\Models\ArticleExpressionSetList;
use Modules\ArticleExpression\Models\ArticleExpressionTranslation;

class ArticleExpressionHelpers
{
    /**
     * Generate JSON representation of the expression set list items
     *
     * @param ArticleExpressionSet $expressionSet
     * @param array $supportedLocales The supported locales for translations (default: ['bn', 'hi'])
     * @return array
     */
    public static function generateJsonData(ArticleExpressionSet $expressionSet, array $supportedLocales = ['bn', 'hi'])
    {
        // Build the root structure with expression set properties
        $data = [
            'id' => $expressionSet->id,
            'article_id' => $expressionSet->article_id,
            'title' => $expressionSet->title,
            'content' => $expressionSet->content,
            'display_order' => $expressionSet->display_order,
            'title_translation' => [
                'bn' => $expressionSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $expressionSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $expressionSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $expressionSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($expressionSet->column_order) ?
                json_decode($expressionSet->column_order, true) :
                ($expressionSet->column_order ?? ['expression', 'meaning', 'example_sentence']),
            'expression_set_lists' => []
        ];

        $expressionLists = ArticleExpressionSetList::where('article_expression_set_id', $expressionSet->id)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($expressionLists->isNotEmpty()) {
            foreach ($expressionLists as $expressionList) {
                $translations = $expressionList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'expression_translation' => $translation->expression_translation ?? '',
                        'expression_transliteration' => $translation->expression_transliteration ?? '',
                        'meaning_translation' => $translation->meaning_translation ?? '',
                        'example_sentence_translation' => $translation->example_sentence_translation ?? '',
                        'example_sentence_transliteration' => $translation->example_sentence_transliteration ?? '',
                    ];
                }

                // Get pronunciation data using Spatie's getTranslation method
                $pronunciation = [
                    'bn' => $expressionList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $expressionList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                $data['expression_set_lists'][] = [
                    'id' => $expressionList->id,
                    'expression' => $expressionList->expression,
                    'slug' => $expressionList->slug,
                    'pronunciation' => $pronunciation,
                    'parts_of_speech' => $expressionList->parts_of_speech,
                    'meaning' => $expressionList->meaning,
                    'example_sentence' => $expressionList->example_sentence,
                    'display_order' => $expressionList->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['expression_set_lists'] = [
                [
                    'expression' => '',
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
                            'expression_translation' => '',
                            'expression_transliteration' => '',
                            'meaning_translation' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'expression_translation' => '',
                            'expression_transliteration' => '',
                            'meaning_translation' => '',
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
     * Process JSON data to update or create expression list entries
     *
     * @param ArticleExpressionSet|null $expressionSet Existing expression set or null to create a new one
     * @param string $jsonData
     * @param array $debugInfo Optional array to store debug information
     * @return ArticleExpressionSet The processed expression set instance (existing or newly created)
     * @throws \Exception If there are issues processing the data
     */
    public static function processJsonData(?ArticleExpressionSet $expressionSet, $jsonData, array &$debugInfo = [])
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
            throw new \Exception("JSON must be an object with expression set data");
        }

        // If expressionSet is null, create a new ArticleExpressionSet instance
        $creatingNew = false;
        if ($expressionSet === null) {
            $creatingNew = true;
            $expressionSet = new ArticleExpressionSet();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Creating new ArticleExpressionSet instance";
            }
        } else {
            // Debug info for existing expression set
            if (is_array($debugInfo)) {
                $debugInfo[] = "Using article_expression_set_id: {$expressionSet->id}";
            }
        }

        // Verify the data has the expected structure
        if (!isset($data['expression_set_lists']) || !is_array($data['expression_set_lists'])) {
            throw new \Exception("Missing or invalid 'expression_set_lists' array in JSON data");
        }

        // 1. Update or create the Article Expression Set (parent) data
        if (isset($data['title']) || $creatingNew) {
            // Process column_order - ensure it's stored as a JSON string in the database
            $columnOrder = null;
            if (isset($data['column_order'])) {
                // Ensure column_order is saved as a JSON string, not an array
                $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
            }

            // Prepare expression set data
            $expressionSetData = [
                'title' => $data['title'] ?? ($creatingNew ? '' : $expressionSet->title),
                'content' => $data['content'] ?? ($creatingNew ? '' : $expressionSet->content),
                'display_order' => $data['display_order'] ?? ($creatingNew ? 1 : $expressionSet->display_order),
                'column_order' => $columnOrder,
            ];
            
            // Add article_id only when creating new
            if ($creatingNew && isset($data['article_id'])) {
                $expressionSetData['article_id'] = $data['article_id'];
            }
            
            if ($creatingNew) {
                // Set all fields on the new model
                foreach ($expressionSetData as $key => $value) {
                    $expressionSet->$key = $value;
                }
                // Save the new expression set
                $expressionSet->save();
            } else {
                // Update the existing expression set
                $expressionSet->update($expressionSetData);
            }

            if (is_array($debugInfo)) {
                $debugInfo[] = "Updated ArticleExpressionSet ID: {$expressionSet->id}";
            }

            // Handle title_translation directly for bn and hi
            if (isset($data['title_translation'])) {
                // Set Bengali title translation
                if (isset($data['title_translation']['bn'])) {
                    $expressionSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                }

                // Set Hindi title translation
                if (isset($data['title_translation']['hi'])) {
                    $expressionSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
            }

            // Handle content_translation directly for bn and hi
            if (isset($data['content_translation'])) {
                // Set Bengali content translation
                if (isset($data['content_translation']['bn'])) {
                    $expressionSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                }

                // Set Hindi content translation
                if (isset($data['content_translation']['hi'])) {
                    $expressionSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
            }

            // Save translations
            $expressionSet->save();
        }

        // Initialize arrays to track processed IDs
        $processedIds = [];
        $savedTranslationIds = [];
        $displayOrder = 1;

        // 2. Process each expression in the array
        // Filter out items with empty expressions
        $validItems = [];
        foreach ($data['expression_set_lists'] as $index => $item) {
            if (!empty($item['expression'])) {
                $validItems[] = $item;
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Skipped item #" . ($index + 1) . " with empty expression";
                }
            }
        }

        // Process only valid items
        foreach ($validItems as $index => $item) {
            // Create or update expression set list using slug as the primary lookup key
            $slug = $item['slug'] ?? Str::slug($item['expression']);

            // Try to find by slug first if we have a saved expressionSet
            $expressionList = null;
            if ($expressionSet->exists) {
                $expressionList = ArticleExpressionSetList::where('slug', $slug)
                    ->where('article_expression_set_id', $expressionSet->id)
                    ->first();
            }

            if (!$expressionList) {
                // Create new record if not found by slug
                $expressionList = new ArticleExpressionSetList();
                $expressionList->article_expression_set_id = $expressionSet->id;
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Creating new expression list item with slug: {$slug}";
                }
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Updating existing expression list item with slug: {$slug}";
                }
            }

            $expressionList->expression = $item['expression'];
            $expressionList->parts_of_speech = $item['parts_of_speech'] ?? '';
            $expressionList->meaning = $item['meaning'] ?? '';
            $expressionList->example_sentence = $item['example_sentence'] ?? '';
            $expressionList->display_order = $item['display_order'] ?? $displayOrder++;

            // Generate a unique slug to avoid duplicates
            $baseSlug = Str::slug($item['expression']) ?: Str::uuid();
            $slug = $baseSlug;
            $counter = 1;

            // Check if this is a new record or we're changing the slug
            if (!$expressionList->exists || $expressionList->slug !== $baseSlug) {
                // Check for duplicate slugs and make unique if needed
                while (ArticleExpressionSetList::where('article_expression_set_id', $expressionSet->id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $expressionList->id ?? 0)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
            }

            $expressionList->slug = $slug;

            // Handle pronunciation using Spatie's translatable functionality
            if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                // Handle Bengali pronunciation
                if (isset($item['pronunciation']['bn'])) {
                    $expressionList->setTranslation('pronunciation', 'bn', $item['pronunciation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali pronunciation for expression: {$item['pronunciation']['bn']}";
                    }
                }

                // Handle Hindi pronunciation
                if (isset($item['pronunciation']['hi'])) {
                    $expressionList->setTranslation('pronunciation', 'hi', $item['pronunciation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi pronunciation for expression: {$item['pronunciation']['hi']}";
                    }
                }
            } else {
                // Initialize empty pronunciations if not provided
                $expressionList->setTranslation('pronunciation', 'bn', '');
                $expressionList->setTranslation('pronunciation', 'hi', '');
            }

            $expressionList->save();

            $processedIds[] = $expressionList->id;

            // Process translations - only handle sequential format
            if (isset($item['translations']) && is_array($item['translations'])) {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Processing translations for expression: {$item['expression']}";
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
                    $tr = ArticleExpressionTranslation::where('article_expression_set_list_id', $expressionList->id)
                        ->where('locale', $locale)
                        ->first();

                    if (!$tr) {
                        $tr = new ArticleExpressionTranslation();
                        $tr->article_expression_set_list_id = $expressionList->id;
                        $tr->locale = $locale;
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Created new translation for locale: {$locale}";
                        }
                    } else {
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Updating existing translation for locale: {$locale}";
                        }
                    }

                    $tr->expression_translation = $trData['expression_translation'] ?? '';
                    $tr->expression_transliteration = $trData['expression_transliteration'] ?? '';
                    $tr->meaning_translation = $trData['meaning_translation'] ?? '';
                    $tr->example_sentence_translation = $trData['example_sentence_translation'] ?? '';
                    $tr->example_sentence_transliteration = $trData['example_sentence_transliteration'] ?? '';
                    $tr->save();
                    $savedTranslationIds[] = $tr->id;
                }
            }
        }

        // Delete missing translations
        if ($expressionSet->exists && !empty($processedIds)) {
            ArticleExpressionTranslation::whereIn('article_expression_set_list_id', $processedIds)
                ->whereNotIn('id', $savedTranslationIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted translations that are no longer present in the input";
            }

            // Delete records not in the processed list
            $deletedCount = ArticleExpressionSetList::where('article_expression_set_id', $expressionSet->id)
                ->whereNotIn('id', $processedIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted {$deletedCount} expression list items that are no longer present in the input";
            }
        }

        return $expressionSet;
    }

    /**
     * Generate initial JSON structure for creating a new expression set
     *
     * @param int|null $articleId Optional article ID to associate with the new expression set
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
            'column_order' => ['expression', 'meaning', 'example_sentence'],
            'expression_set_lists' => [
                [
                    'expression' => '',
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
                            'expression_translation' => '',
                            'expression_transliteration' => '',
                            'meaning_translation' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'expression_translation' => '',
                            'expression_transliteration' => '',
                            'meaning_translation' => '',
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
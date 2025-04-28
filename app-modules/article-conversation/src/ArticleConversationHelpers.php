<?php

namespace Modules\ArticleConversation;

use Illuminate\Support\Str;
use Modules\ArticleConversation\Models\ArticleConversationSet;
use Modules\ArticleConversation\Models\ArticleConversationMessage;
use Modules\ArticleConversation\Models\ArticleConversationTranslation;

class ArticleConversationHelpers
{
    /**
     * Generate JSON representation of the conversation set messages
     *
     * @param ArticleConversationSet $conversationSet
     * @param array $supportedLocales The supported locales for translations (default: ['bn', 'hi'])
     * @return array
     */
    public static function generateJsonData(ArticleConversationSet $conversationSet, array $supportedLocales = ['bn', 'hi'])
    {
        // Build the root structure with conversation set properties
        $data = [
            'id' => $conversationSet->id,
            'article_id' => $conversationSet->article_id,
            'title' => $conversationSet->title,
            'content' => $conversationSet->content,
            'display_order' => $conversationSet->display_order,
            'title_translation' => [
                'bn' => $conversationSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $conversationSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $conversationSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $conversationSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($conversationSet->column_order) ?
                json_decode($conversationSet->column_order, true) :
                ($conversationSet->column_order ?? ['speaker', 'message']),
            'conversation_messages' => []
        ];

        $conversationMessages = ArticleConversationMessage::where('article_conversation_set_id', $conversationSet->id)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($conversationMessages->isNotEmpty()) {
            foreach ($conversationMessages as $conversationMessage) {
                $translations = $conversationMessage->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'message_translation' => $translation->message_translation ?? '',
                        'message_transliteration' => $translation->message_transliteration ?? '',
                        'speaker_translation' => $translation->speaker_translation ?? '',
                    ];
                }

                $data['conversation_messages'][] = [
                    'id' => $conversationMessage->id,
                    'speaker' => $conversationMessage->speaker,
                    'message' => $conversationMessage->message,
                    'display_order' => $conversationMessage->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['conversation_messages'] = [
                [
                    'speaker' => '',
                    'message' => '',
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'message_translation' => '',
                            'message_transliteration' => '',
                            'speaker_translation' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'message_translation' => '',
                            'message_transliteration' => '',
                            'speaker_translation' => '',
                        ]
                    ]
                ]
            ];
        }

        return $data;
    }

    /**
     * Process JSON data to update or create conversation message entries
     *
     * @param ArticleConversationSet|null $conversationSet Existing conversation set or null to create a new one
     * @param string $jsonData
     * @param array $debugInfo Optional array to store debug information
     * @return ArticleConversationSet The processed conversation set instance (existing or newly created)
     * @throws \Exception If there are issues processing the data
     */
    public static function processJsonData(?ArticleConversationSet $conversationSet, $jsonData, array &$debugInfo = [])
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
            throw new \Exception("JSON must be an object with conversation set data");
        }

        // If conversationSet is null, create a new ArticleConversationSet instance
        $creatingNew = false;
        if ($conversationSet === null) {
            $creatingNew = true;
            $conversationSet = new ArticleConversationSet();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Creating new ArticleConversationSet instance";
            }
        } else {
            // Debug info for existing conversation set
            if (is_array($debugInfo)) {
                $debugInfo[] = "Using article_conversation_set_id: {$conversationSet->id}";
            }
        }

        // Verify the data has the expected structure
        if (!isset($data['conversation_messages']) || !is_array($data['conversation_messages'])) {
            throw new \Exception("Missing or invalid 'conversation_messages' array in JSON data");
        }

        // 1. Update or create the Article Conversation Set (parent) data
        if (isset($data['title']) || $creatingNew) {
            // Process column_order - ensure it's stored as a JSON string in the database
            $columnOrder = null;
            if (isset($data['column_order'])) {
                // Ensure column_order is saved as a JSON string, not an array
                $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
            }

            // Prepare conversation set data
            $conversationSetData = [
                'title' => $data['title'] ?? ($creatingNew ? '' : $conversationSet->title),
                'content' => $data['content'] ?? ($creatingNew ? '' : $conversationSet->content),
                'display_order' => $data['display_order'] ?? ($creatingNew ? 1 : $conversationSet->display_order),
                'column_order' => $columnOrder,
            ];
            
            // Add article_id only when creating new
            if ($creatingNew && isset($data['article_id'])) {
                $conversationSetData['article_id'] = $data['article_id'];
            }
            
            if ($creatingNew) {
                // Set all fields on the new model
                foreach ($conversationSetData as $key => $value) {
                    $conversationSet->$key = $value;
                }
                // Save the new conversation set
                $conversationSet->save();
            } else {
                // Update the existing conversation set
                $conversationSet->update($conversationSetData);
            }

            if (is_array($debugInfo)) {
                $debugInfo[] = "Updated ArticleConversationSet ID: {$conversationSet->id}";
            }

            // Handle title_translation directly for bn and hi
            if (isset($data['title_translation'])) {
                // Set Bengali title translation
                if (isset($data['title_translation']['bn'])) {
                    $conversationSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                }

                // Set Hindi title translation
                if (isset($data['title_translation']['hi'])) {
                    $conversationSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
            }

            // Handle content_translation directly for bn and hi
            if (isset($data['content_translation'])) {
                // Set Bengali content translation
                if (isset($data['content_translation']['bn'])) {
                    $conversationSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                }

                // Set Hindi content translation
                if (isset($data['content_translation']['hi'])) {
                    $conversationSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                    if (is_array($debugInfo)) {
                        $debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
            }

            // Save translations
            $conversationSet->save();
        }

        // Initialize arrays to track processed IDs
        $processedIds = [];
        $savedTranslationIds = [];
        $displayOrder = 1;

        // 2. Process each conversation message in the array
        // Filter out items with empty messages
        $validItems = [];
        foreach ($data['conversation_messages'] as $index => $item) {
            if (!empty($item['message'])) {
                $validItems[] = $item;
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Skipped message #" . ($index + 1) . " with empty message";
                }
            }
        }

        // Process only valid items
        foreach ($validItems as $index => $item) {
            // Try to find an existing message or create a new one
            $conversationMessage = null;
            if (isset($item['id']) && $conversationSet->exists) {
                $conversationMessage = ArticleConversationMessage::where('id', $item['id'])
                    ->where('article_conversation_set_id', $conversationSet->id)
                    ->first();
            }

            if (!$conversationMessage) {
                // Create new record if not found by ID
                $conversationMessage = new ArticleConversationMessage();
                $conversationMessage->article_conversation_set_id = $conversationSet->id;
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Creating new conversation message";
                }
            } else {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Updating existing conversation message ID: {$conversationMessage->id}";
                }
            }

            $conversationMessage->speaker = $item['speaker'] ?? '';
            $conversationMessage->message = $item['message'];
            $conversationMessage->display_order = $item['display_order'] ?? $displayOrder++;
            $conversationMessage->save();

            $processedIds[] = $conversationMessage->id;

            // Process translations - only handle sequential format
            if (isset($item['translations']) && is_array($item['translations'])) {
                if (is_array($debugInfo)) {
                    $debugInfo[] = "Processing translations for message: {$item['message']}";
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
                    $tr = ArticleConversationTranslation::where('article_conversation_message_id', $conversationMessage->id)
                        ->where('locale', $locale)
                        ->first();

                    if (!$tr) {
                        $tr = new ArticleConversationTranslation();
                        $tr->article_conversation_message_id = $conversationMessage->id;
                        $tr->locale = $locale;
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Created new translation for locale: {$locale}";
                        }
                    } else {
                        if (is_array($debugInfo)) {
                            $debugInfo[] = "Updating existing translation for locale: {$locale}";
                        }
                    }

                    $tr->message_translation = $trData['message_translation'] ?? '';
                    $tr->message_transliteration = $trData['message_transliteration'] ?? '';
                    $tr->speaker_translation = $trData['speaker_translation'] ?? '';
                    $tr->save();
                    $savedTranslationIds[] = $tr->id;
                }
            }
        }

        // Delete missing translations
        if ($conversationSet->exists && !empty($processedIds)) {
            ArticleConversationTranslation::whereIn('article_conversation_message_id', $processedIds)
                ->whereNotIn('id', $savedTranslationIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted translations that are no longer present in the input";
            }

            // Delete records not in the processed list
            $deletedCount = ArticleConversationMessage::where('article_conversation_set_id', $conversationSet->id)
                ->whereNotIn('id', $processedIds)
                ->delete();
            if (is_array($debugInfo)) {
                $debugInfo[] = "Deleted {$deletedCount} conversation messages that are no longer present in the input";
            }
        }

        return $conversationSet;
    }

    /**
     * Generate initial JSON structure for creating a new conversation set
     *
     * @param int|null $articleId Optional article ID to associate with the new conversation set
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
            'column_order' => ['speaker', 'message'],
            'conversation_messages' => [
                [
                    'speaker' => '',
                    'message' => '',
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'message_translation' => '',
                            'message_transliteration' => '',
                            'speaker_translation' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'message_translation' => '',
                            'message_transliteration' => '',
                            'speaker_translation' => '',
                        ]
                    ]
                ]
            ]
        ];

        return json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
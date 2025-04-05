# Englishing Project Database Structure

## Core Modules

### User Module
- users(name, email, password, etc.)
- user_profiles(user_id, bio, avatar, etc.)

### Article Module
- courses(title, slug, description, display_order, title_translation, description_translation)
  - Relations: has many articles, belongs to user

- articles(course_id, title, slug, content, display_order, title_translation, content_translation)
  - Relations: belongs to course, belongs to user

## Article Extensions

### Article Word Module
- article_word_sets(article_id, display_order, title, content, column_order, static_content_1, static_content_2, title_translation, content_translation)
  - Relations: belongs to article, has many article_word_set_lists

- article_word_set_lists(article_word_set_id, display_order, word, slug, phonetic, pronunciation, parts_of_speech, static_content_1, static_content_2, meaning, example_sentence, example_expression, example_expression_meaning)
  - Relations: belongs to article_word_set, has many article_word_translations
  - Notes: pronunciation is Spatie Translatable

- article_word_translations(article_word_set_list_id, word_translation, word_transliteration, example_sentence_translation, example_sentence_transliteration, example_expression_translation, example_expression_transliteration, locale, source)
  - Relations: belongs to article_word_set_list
  - Constraints: unique(article_word_set_list_id, locale)

### Article Sentence Module
- article_sentence_sets(article_id, display_order, title, content, title_translation, content_translation)
  - Relations: belongs to article, has many article_sentence_set_lists
  - Notes: title_translation and content_translation are Spatie Translatable

- article_sentence_set_lists(article_sentence_set_id, sentence, slug, display_order, pronunciation)
  - Relations: belongs to article_sentence_set, has many article_sentence_translations
  - Notes: pronunciation is Spatie Translatable

- article_sentence_translations(article_sentence_set_list_id, translation, transliteration, locale)
  - Relations: belongs to article_sentence_set_list
  - Constraints: unique(article_sentence_set_list_id, locale)

### Article Double Sentence Module
- article_double_sentence_sets(article_id, display_order, title, content, title_translation, content_translation)
  - Relations: belongs to article, has many article_double_sentence_set_lists

- article_double_sentence_set_lists(article_double_sentence_set_id, sentence_1, sentence_1_slug, sentence_2, sentence_2_slug, display_order, pronunciation_1, pronunciation_2)
  - Relations: belongs to article_double_sentence_set, has many article_double_sentence_translations
  - Notes: pronunciation_1 and pronunciation_2 are Spatie Translatable

- article_double_sentence_translations(article_double_sentence_set_list_id, sentence_1_translation, sentence_2_translation, sentence_1_transliteration, sentence_2_transliteration, locale)
  - Relations: belongs to article_double_sentence_set_list
  - Constraints: unique(article_double_sentence_set_list_id, locale)

### Article Expression Module
- article_expression_sets(article_id, display_order, title, content, title_translation, content_translation)
  - Relations: belongs to article, has many article_expression_set_lists

- article_expression_set_lists(article_expression_set_id, expression, slug, display_order, meaning, example_sentence, pronunciation)
  - Relations: belongs to article_expression_set, has many article_expression_translations
  - Notes: pronunciation is Spatie Translatable

- article_expression_translations(article_expression_set_list_id, expression_translation, expression_transliteration, example_sentence_translation, example_sentence_transliteration, locale, source)
  - Relations: belongs to article_expression_set_list
  - Constraints: unique(article_expression_set_list_id, locale)

### Article Conversation Module
- article_conversations(article_id, title, slug, content, title_translation, content_translation)
  - Relations: belongs to article, has many article_conversation_messages

- article_conversation_messages(article_conversation_id, speaker, message, slug, display_order, pronunciation)
  - Relations: belongs to article_conversation, has many article_conversation_message_translations
  - Notes: pronunciation is Spatie Translatable, speaker is a string (speaker_1, speaker_2, speaker_3)

- article_conversation_message_translations(article_conversation_message_id, translation, transliteration, locale)
  - Relations: belongs to article_conversation_message
  - Constraints: unique(article_conversation_message_id, locale)

## Standalone Modules

### Word Module
- words(word, slug)
  - Relations: has many meanings, has one pronunciation, has many connections

- word_meanings(word_id, meaning, slug)
  - Relations: belongs to word, has one translation

- word_pronunciations(word_id, bn_pronunciation, hi_pronunciation, es_pronunciation)
  - Relations: belongs to word

- word_meaning_translations(word_meaning_id, bn_meaning, hi_meaning, es_meaning)
  - Relations: belongs to word_meaning, has one transliteration

- word_meaning_transliterations(word_meaning_translation_id, bn_transliteration, hi_transliteration, es_transliteration)
  - Relations: belongs to word_meaning_translation

- word_connections(word_id_1, word_id_2, type)
  - Relations: connects two words
  - Notes: type is a string (synonym, antonym, etc.)

### Sentence Module
- sentences(sentence, slug, source)
  - Relations: has one pronunciation, has one translation

- sentence_pronunciations(sentence_id, bn_pronunciation, hi_pronunciation, es_pronunciation)
  - Relations: belongs to sentence

- sentence_translations(sentence_id, bn_sentence, hi_sentence, es_sentence)
  - Relations: belongs to sentence, has one transliteration

- sentence_transliterations(sentence_translation_id, bn_transliteration, hi_transliteration, es_transliteration)
  - Relations: belongs to sentence_translation

### Expression Module
- expressions(expression, type, slug)
  - Relations: has many meanings, has one pronunciation

- expression_meanings(expression_id, meaning)
  - Relations: belongs to expression, has one translation

- expression_pronunciations(expression_id, bn_pronunciation, hi_pronunciation, es_pronunciation)
  - Relations: belongs to expression

- expression_meaning_translations(expression_meaning_id, bn_meaning, hi_meaning, es_meaning)
  - Relations: belongs to expression_meaning, has one transliteration

- expression_meaning_transliterations(expression_meaning_translation_id, bn_transliteration, hi_transliteration, es_transliteration)
  - Relations: belongs to expression_meaning_translation

- expression_connections(expression_id_1, expression_id_2, type)
  - Relations: connects two expressions
  - Notes: type is a string (similar, opposite, etc.)

pronunciation should be non english locale.
phonetic should be for english locale

# words
words(id, word, slug, pronunciation, phonetic, part_of_speech, source)   # pronunciation will be spatie Translatable
word_meanings(id, word_id, meaning, slug, source, display_order)   # keep english meaning only
word_translations(id, word_id, meaning_id (nullable), translation, transliteration, locale, source) # $table->unique(['word_id', 'locale', 'slug']);
word_connections(word_id_1, word_id_2, type[synonyms, antonyms])  

word_translations

```relation
word can have multiple meanings
word can have multiple translations
meaning can have only one translations
word_connections is a pivot table
````

# sentence
sentences(id, sentence, slug, source, pronunciation) # pronunciation will be spatie Translatable
sentence_translations(id, sentence_id, translation, transliteration,  slug, locale, source, )  # $table->unique(['sentence_id', 'locale', 'slug']); 

```relation
sentence can have only one translations for each locale
```

# expression
expressions(id, expression, type, slug)  
expression_meanings(id, expression_id, meaning, pronunciation)   # pronunciation will be spatie Translatable
expression_translations(id, expression_id, expression_meaning_id, translation, transliteration, slug, locale, source)  # $table->unique(['expression_id', 'locale', 'slug']);
expression_connections(expression_id_1, expression_id_2, type[synonyms, antonyms])  

```relation
expression can have multiple meanings
expression has only one pronunciation
expression can have multiple translations
meaning can have only one translations
translation can have only one transliteration
expression_connections is a pivot table
````

# articles
courses(id, user_id, title, content, slug, title_translation, content_translation, status)   title_translation, content_translation will be spatie Translatable
articles(id, user_id, course_id, type, title, slug, content, display_order, status, excerpt, is_premium, scratchpad, title_translation, content_translation, excerpt_translation)  # title_translation, content_translation, excerpt_translation spatie Translatable

in article model make a helper function for getting title and link of associated article for same series with section. just an array having article id, title, slug, title_translation
like following



# article-table
article_tables(id, article_id, display_order, title, user_id, type)  
article_table_columns(id, article_table_id, display_order, heading)  
article_table_rows(id, article_table_id, display_order)  
article_table_cells(id, article_table_row_id, article_table_column_id, content, content_slug, content_meaning, content_meaning_slug)  
article_table_cell_translations(id, article_table_cell_id, bn_content_meaning, hi_content_meaning, es_content_meaning)  
article_table_cell_transliterations(id, article_table_cell_translation_id, bn_transliteration, hi_transliteration, es_transliteration)


# article-word
article_word_sets (id, article_id, display_order, title, content, column_order, static_content_1, static_content_2, title_translation, content_translation ) # eg column_order ['word', 'meaning', 'meaning_translations', 'example_sentences', 'example_sentence_translations', 'expression', 'expression_meaning', 'expression_meaning_translation']
article_word_set_lists(id, article_word_set_id, display_order, word, slug, phonetic, pronunciation, parts_of_speech, static_content_1, static_content_2, meaning, example_sentence, example_expression, example_expression_meaning ) # pronunciation will be spatie Translatable  
article_word_translations(id, article_word_set_list_id, word_translation, word_transliteration, example_sentence_translation, example_sentence_transliteration, example_expression_translation, example_expression_transliteration, locale, source, slug) # unique(article_word_set_list_id locale slug)  slug will be generated from word_translation




```relation
article_word_set has many article_word_set_lists
list word has many word translations but each locale have only one
````

# article-sentence
article_sentence_sets(id, article_id, display_order, title, content, title_translation, content_translation) # title_translation and content_translation will be spatie Translatable  
article_sentence_set_lists(id, article_sentence_set_id, sentence, pronunciation, slug, display_order)  # pronunciation will be spatie Translatable 
article_sentence_translations(id, article_sentence_set_list_id, translation, transliteration, locale, slug)  #unique(article_sentence_set_list_id, locale, slug)

```relation
article_sentence_set has many article_sentence_set_lists
list sentence has many translations but each locale have only one
````





# article-expression
article_expression_sets(id, article_id, display_order, title, content, title_translation, content_translation)  
article_expression_set_lists(id, article_expression_set_id, expression, slug, display_order, meaning, example_sentence)  
article_expression_translations(id, article_expression_set_list_id, slug, expression_translation, expression_transliteration, example_sentence_translation, example_sentence_transliteration, locale, source) # unique(article_expression_set_list_id locale slug)

```relation
article_expression_set has many article_expression_set_lists
list has many article_expression_translations however each locale have only one relation
````


# article-double-word
article_double_word_sets(id, article_id, display_order, title, content, title_translation, content_translation)  
article_double_word_set_lists(id, article_double_word_set_id, word_1, word_1_slug, word_2, word_2_slug, word_1_meaning
, word_2_meaning, word_1_example_sentence, word_2_example_sentence, display_order)  
article_double_word_translations(id, article_double_word_set_list_id, word_1_translation, word_2_translation, word_1_transliteration, word_2_transliteration, locale, slug) # unique(article_double_word_set_list_id locale slug)

```relation
article_double_word_set has many article_double_word_set_lists
article_double_word_set_list has many article_double_word_translation but each locale have only one
````

# article triple word
article_triple_word_sets(id, article_id, display_order, title, content, title_translation, content_translation)  
article_triple_word_set_lists(id, article_triple_word_set_id, word_1, word_1_slug, word_2, word_2_slug, word_3, word_3_slug, word_1_meaning, word_2_meaning, word_3_meaning, word_1_example_sentence, word_2_example_sentence, word_3_example_sentence, display_order)  
article_triple_word_translations(id, article_triple_word_set_list_id, word_1_translation, word_2_translation, word_3_translation, word_1_transliteration, word_2_transliteration, word_3_transliteration, locale, slug) # unique(article_triple_word_set_list_id locale slug)

```relation
article_triple_word_set has many article_triple_word_set_lists
article_triple_word_set_list has many article_triple_word_translation but each locale have only one
````

# article-double-sentence
article_double_sentence_sets(id, article_id, display_order, title, content, title_translation, content_translation)  
article_double_sentence_set_lists(id, article_double_sentence_set_id, sentence_1, sentence_1_slug, sentence_2, sentence_2_slug, display_order)  
article_double_sentence_translations(id, article_double_sentence_set_list_id, sentence_1_translation, sentence_2_translation, sentence_1_transliteration, sentence_2_transliteration, locale, slug) # unique(article_double_sentence_set_list_id locale slug)

```relation
article_double_sentence_set has many article_double_sentence_set_lists
article_double_sentence_set_list has many article_double_sentence_translation but each locale have only one
````



# article-conversation
article_conversations(id, article_id, title, slug, content, title_translation, content_translation)
article_conversation_messages(id, article_conversation_id, speaker[string: speaker_1, speaker_2, speaker_3], message, slug, display_order )
article_conversation_message_translations(id, article_conversation_message_id, locale, slug)

```relation
conversation has many messages
message has many translation however each locale have only one translation
```















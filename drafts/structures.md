# words
words(id, word, word_250, slug)  
word_meanings(id, word_id, meaning, slug)  
word_pronunciations (id, word_id, bn_pronunciation, hi_pronunciation, es_pronunciation)
word_meaning_translations(id, word_meaning_id, bn_meaning, hi_meaning, es_meaning)
word_meaning_transliterations(id, word_meaning_translation_id, bn_transliteration, hi_transliteration, es_transliteration)
word_connections(word_id_1, word_id_2, type[synonyms, antonyms])  

```relation
word can have multiple meanings
word has only one pronunciation
meaning can have only one translations
translation can have only one transliteration
word_connections is a pivot table
````

# sentence
sentences(id, sentence, slug, source)  # sentence itself a meaning
sentence_pronunciations (id, word_id, bn_pronunciation, hi_pronunciation, es_pronunciation)
sentence_translations(id, sentence_id, bn_sentence, hi_sentence, es_sentence)  
sentence_transliterations(id, sentence_translation_id, bn_transliteration, hi_transliteration, es_transliteration)

```relation
sentence can have only one translations
sentence can have only one pronunciation
sentence_translations can have only one transliteration
```

# expression
expressions(id, expression, type, slug)  
expression_meanings(id, expression_id, meaning)  
expression_pronunciations (id, word_id, bn_pronunciation, hi_pronunciation, es_pronunciation)
expression_meaning_translations(id, expression_meaning_id, bn_meaning, hi_meaning, es_meaning)  
expression_meaning_transliterations(id, expression_meaning_translation_id, bn_transliteration, hi_transliteration, es_transliteration)
expression_connections(expression_id_1, expression_id_2, type[synonyms, antonyms])  

```relation
expression can have multiple meanings
expression has only one pronunciation
meaning can have only one translations
translation can have only one transliteration
expression_connections is a pivot table
````

# articles
series(id, user_id, title, content)  
series_translations(id, series_id, bn_title, hi_title, es_title, bn_content, hi_content, es_content)
sections(id, user_id, series_id, title, content)  
section_translations(id, section_id, bn_title, hi_title, es_title, bn_content, hi_content, es_content)
articles(id, user_id, series_id, section_id, type, title, content, excerpt, is_premium, scratchpad)  
article_translations(id, article_id, bn_title, hi_title, es_title, bn_content, hi_content, es_content, bn_excerpt, hi_excerpt, es_excerpt)

# article-table
article_tables(id, article_id, display_order, title, user_id, type)  
article_table_columns(id, article_table_id, display_order, heading)  
article_table_rows(id, article_table_id, display_order)  
article_table_cells(id, article_table_row_id, article_table_column_id, content, content_slug, content_meaning, content_meaning_slug)  
article_table_cell_translations(id, article_table_cell_id, bn_content_meaning, hi_content_meaning, es_content_meaning)  
article_table_cell_transliterations(id, article_table_cell_translation_id, bn_transliteration, hi_transliteration, es_transliteration)


# article-word
article_word_sets (id, article_id, display_order, title, content, column_order ) # eg column_order ['word', 'meaning', 'meaning_translations', 'example_sentences', 'example_sentence_translations', 'expression', 'expression_meaning', 'expression_meaning_translation']
article_word_set_lists(id, article_word_set_id, display_order, word, slug, position, phonetic, parts_of_speech)  
article_word_meanings(id, article_word_set_list_id, meaning, display_order)  
article_word_meaning_translations(id, article_word_meaning_id, bn_meaning, hi_meaning, es_meaning)  
article_word_meaning_transliterations(id, article_word_meaning_translation_id, bn_transliteration, hi_transliteration, es_transliteration)
article_word_example_sentences(id, article_word_set_list_id, display_order, sentence, slug)  
article_word_example_sentence_translations(id, article_word_example_sentence_id, bn_sentence, hi_sentence, es_sentence)  
article_word_example_sentence_transliterations(id, article_word_example_sentence_translation_id, bn_transliteration, hi_transliteration, es_transliteration)


article_word_example_expressions(id, article_word_set_list_id, expression, slug)  
article_word_example_expression_meanings(id, article_word_example_expression_id, meaning)  
article_word_example_expression_meaning_translations(id, article_word_example_expression_meaning_id, bn_meaning, hi_meaning, es_meaning)  
article_word_example_expression_meaning_transliterations(id, article_word_example_expression_meaning_translation_id, bn_transliteration, hi_transliteration, es_transliteration)



# article-sentence
article_sentence_sets(id, article_id, display_order, title, content)  
article_sentence_set_lists(id, article_sentence_set_id, sentence, slug, display_order)  
article_sentence_translations(id, article_sentence_set_list_id, bn_meaning, hi_meaning, es_meaning)  
article_sentence_transliterations(id, article_sentence_translation_id, bn_transliteration, hi_transliteration, es_transliteration)


# article-expression
article_expression_sets(id, article_id, display_order, title)  
article_expression_set_lists(id, article_expression_set_id, expression, slug, display_order)  
article_expression_meanings(id, article_expression_set_list_id, meaning)  
article_expression_meaning_translations(id, article_expression_meaning_id, bn_meaning, hi_meaning, es_meaning)  
article_expression_meaning_transliterations(id, article_expression_meaning_translation_id, bn_transliteration, hi_transliteration, es_transliteration)

article_expression_example_sentences(id, article_expression_set_list_id, sentence, slug)  
article_expression_example_sentence_translations(id, article_expression_example_sentence_id, bn_sentence, hi_sentence, es_sentence)  
article_expression_example_sentence_transliterations(id, article_expression_example_sentence_translation_id, bn_transliteration, hi_transliteration, es_transliteration)



# article-conversation
conversations(id, article_id, title, slug, content, excerpt, display_order, is_premium, scratchpad)
conversation_messages(id, conversation_id, speaker[string: speaker_1, speaker_2, speaker_3], message, slug, display_order )
conversation_message_translations(id, conversation_message_id, bn_message, hi_message, es_message)














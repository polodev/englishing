please analyze migration file for article sentence
app-modules/article-sentence/database/migrations/2025_04_05_000001_create_article_sentence_sets_table.php
app-modules/article-sentence/database/migrations/2025_04_05_000002_create_article_sentence_set_lists_table.php
app-modules/article-sentence/database/migrations/2025_04_05_000003_create_article_sentence_translations_table.php



please analyze old sample file
public/sample-data/article-sentence/old-article-sentence-set-list-sample.json

please analyze new sample file 
public/sample-data/article-sentence/article-sentence-set-list-sample.json

difference is sentence_set_lists now nested. in root added sentence set seeding data. 

now need to update following volt component
app-modules/article-sentence/resources/views/livewire/article-sentence--article-sentence-set-list-edit-using-json.blade.php


it will update sentence set data from now on. 
and for sentence set list it will be as usual.


in case of sentence set data please don't update 2 things 1. id, 2, article_id 

when initially editor load with initial data, data representation will be exactly like sample data. 
all 3 entity data. sentence set, sentence set list, sentence set list translation

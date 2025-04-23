please analzye migration file for article expression
app-modules/article-expression/database/migrations/2025_04_05_000001_create_article_expression_sets_table.php
app-modules/article-expression/database/migrations/2025_04_05_000002_create_article_expression_set_lists_table.php
app-modules/article-expression/database/migrations/2025_04_05_000003_create_article_expression_translations_table.php



please analzye old sample file
public/sample-data/article-expression/old-article-expression-set-list-sample.json

please analzye new sample file 
public/sample-data/article-expression/article-expression-set-list-sample.json

difference is expression_set_lists now nested. in root added expression set seeding data. 

now need to update following volt component
app-modules/article-expression/resources/views/livewire/article-expression--article-expression-set-list-edit-using-json.blade.php


it will update expression set data from now on. 
and for word set list it will be as usual.


in case of expression set data please don't update 2 things 1. id, 2, article_id 

when initially editor load with initial data, data representation will be exactly like sample data. 
all 3 entity data. expression set, expression set list, expression set list translation

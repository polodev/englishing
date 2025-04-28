# create helper function for all article relation module 
transfer process json and initial json to the helper file 
it will be used in following condition
    - edit 
    - create
    - seeding


please analyze 
app-modules/article-sentence/src/ArticleSentenceHelpers.php
app-modules/article-sentence/resources/views/livewire/article-sentence--article-sentence-set-list-edit-using-json.blade.php

please move process json to the ArticleWordHelpers and generate json Data method in helpers file  and use inside volt component. 

why i am oursourcing method to another file?
This is because currently its used only in edit block. but we will use it in Seeder and Create volt component which don't create yet. 
use static function

currently we have model as we are editing. but we will use it in Seeder and Create volt component which don't create yet. so keep model optional, so that can be used process json elsewhere
<?php

use Illuminate\Support\Facades\Route;
use Modules\Backend\Http\Controllers\ArticleSentenceSetController;
use Modules\Backend\Http\Controllers\WordController;
use Modules\Backend\Http\Controllers\ArticleWordSetController;
use Modules\Backend\Http\Controllers\ArticleExpressionSetController;
use Modules\Backend\Http\Controllers\CourseController;
use Modules\Backend\Http\Controllers\ArticleController;
use Modules\Backend\Http\Controllers\SentenceController;
use Modules\Backend\Http\Controllers\ExpressionController;
use Modules\Backend\Http\Controllers\TagController;
use Modules\Backend\Http\Controllers\ArticleDoubleWordSetController;
use Modules\Backend\Http\Controllers\ArticleTrippleWordSetController;
use Modules\Backend\Http\Controllers\ArticleDoubleSentenceSetController;
use Modules\Backend\Http\Controllers\ArticleConversationController;

Route::middleware(['web', 'auth'])->group(function () {
    $route_arguments = [
      'prefix' => 'dashboard',
      'as' => 'backend::',
    ];
    Route::group($route_arguments, function () {
        # words
        Route::get('/words/index', [WordController::class, 'index'])->name('words.index');
        Route::get('/words/show/{word}', [WordController::class, 'show'])->name('words.show');
        Route::match(['get', 'post'],'/json/words-index-json', [WordController::class, 'index_json'])->name('words.index_json');

        # sentence
        Route::get('/sentences/index', [SentenceController::class, 'index'])->name('sentences.index');
        Route::get('/sentences/show/{sentence}', [SentenceController::class, 'show'])->name('sentences.show');
        Route::delete('/sentences/destroy/{sentence}', [SentenceController::class, 'destroy'])->name('sentences.destroy');
        Route::match(['get', 'post'],'/json/sentences-index-json', [SentenceController::class, 'index_json'])->name('sentences.index_json');

        # expression
        Route::get('/expressions/index', [ExpressionController::class, 'index'])->name('expressions.index');
        Route::get('/expressions/show/{expression}', [ExpressionController::class, 'show'])->name('expressions.show');
        Route::delete('/expressions/destroy/{expression}', [ExpressionController::class, 'destroy'])->name('expressions.destroy');
        Route::match(['get', 'post'],'/json/expressions-index-json', [ExpressionController::class, 'index_json'])->name('expressions.index_json');

        # course
        Route::get('/courses/index', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/show/{course}', [CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/edit/{course}', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/update/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/destroy/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
        Route::match(['get', 'post'],'/json/courses-index-json', [CourseController::class, 'index_json'])->name('courses.index_json');

        # article
        Route::get('/articles/index', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles/store', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/show/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::get('/articles/edit/{article}', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/update/{article}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/destroy/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
        Route::match(['get', 'post'],'/json/articles-index-json', [ArticleController::class, 'index_json'])->name('articles.index_json');
        // API Routes
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('articles/search', [ArticleController::class, 'searchArticles'])->name('articles.search');
        });

        # article word set
        Route::resource('article-word-sets', ArticleWordSetController::class);
        Route::get('article-word-sets/index/json', [ArticleWordSetController::class, 'index_json'])->name('article-word-sets.index_json');


        # tags
        Route::get('/tags/index', [TagController::class, 'index'])->name('tags.index');
        Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
        Route::post('/tags/store', [TagController::class, 'store'])->name('tags.store');
        Route::get('/tags/show/{tag}', [TagController::class, 'show'])->name('tags.show');
        Route::get('/tags/edit/{tag}', [TagController::class, 'edit'])->name('tags.edit');
        Route::put('/tags/update/{tag}', [TagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/destroy/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
        Route::match(['get', 'post'],'/json/tags-index-json', [TagController::class, 'index_json'])->name('tags.index_json');

        # article expression sets
        Route::resource('article-expression-sets', ArticleExpressionSetController::class);
        Route::get('article-expression-sets/index/json', [ArticleExpressionSetController::class, 'index_json'])->name('article-expression-sets.index_json');

        # article sentence sets
        Route::resource('article-sentence-sets', ArticleSentenceSetController::class);
        Route::get('article-sentence-sets/index/json', [ArticleSentenceSetController::class, 'index_json'])->name('article-sentence-sets.index_json');

        # Article Double Word Sets
        Route::resource('article-double-word-sets', ArticleDoubleWordSetController::class);
        Route::get('article-double-word-sets/index/json', [ArticleDoubleWordSetController::class, 'index_json'])->name('article-double-word-sets.index_json');

        # Article Triple Word Sets
        Route::resource('article-triple-word-sets', ArticleTrippleWordSetController::class);
        Route::get('article-triple-word-sets/index/json', [ArticleTrippleWordSetController::class, 'index_json'])->name('article-triple-word-sets.index_json');

        # Article Double Sentence Sets
        Route::resource('article-double-sentence-sets', ArticleDoubleSentenceSetController::class);
        Route::get('article-double-sentence-sets/index/json', [ArticleDoubleSentenceSetController::class, 'index_json'])->name('article-double-sentence-sets.index_json');

        # Article Conversation Sets
        Route::resource('article-conversation-sets', ArticleConversationController::class);
        Route::get('article-conversation-sets/index/json', [ArticleConversationController::class, 'index_json'])->name('article-conversation-sets.index_json');

    });
});

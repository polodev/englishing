<?php

use Illuminate\Support\Facades\Route;
use Modules\Backend\Http\Controllers\SentenceController;
use Modules\Backend\Http\Controllers\WordController;

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


    });
});
<?php

use Illuminate\Support\Facades\Route;
use Modules\Backend\Http\Controllers\WordController;

Route::middleware(['web', 'auth'])->group(function () {
    $route_arguments = [
      'prefix' => 'dashboard',
      'as' => 'backend::',
    ];
    Route::group($route_arguments, function () {
        Route::get('/words/index', [WordController::class, 'index'])->name('words.index');
        Route::get('/words/show/{word}', [WordController::class, 'show'])->name('words.show');
       Route::match(['get', 'post'],'/json/words-index-json', [WordController::class, 'index_json'])->name('words.index_json');
    });
});
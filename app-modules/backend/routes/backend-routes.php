<?php

use Illuminate\Support\Facades\Route;
use Modules\Backend\Http\Controllers\ExpressionController;
use Modules\Backend\Http\Controllers\SentenceController;
use Modules\Backend\Http\Controllers\WordController;
use Modules\Backend\Http\Controllers\CourseController;

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
        Route::delete('/courses/destroy/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
        Route::match(['get', 'post'],'/json/courses-index-json', [CourseController::class, 'index_json'])->name('courses.index_json');
    });
});
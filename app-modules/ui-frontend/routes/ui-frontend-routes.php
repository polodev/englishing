<?php

use Illuminate\Support\Facades\Route;
use Modules\UiFrontend\Http\Controllers\AccountController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\UiFrontend\Http\Controllers\UiFrontendController;

// Route::get('/ui-frontends', [UiFrontendController::class, 'index'])->name('ui-frontends.index');
// Route::get('/ui-frontends/create', [UiFrontendController::class, 'create'])->name('ui-frontends.create');
// Route::post('/ui-frontends', [UiFrontendController::class, 'store'])->name('ui-frontends.store');
// Route::get('/ui-frontends/{ui-frontend}', [UiFrontendController::class, 'show'])->name('ui-frontends.show');
// Route::get('/ui-frontends/{ui-frontend}/edit', [UiFrontendController::class, 'edit'])->name('ui-frontends.edit');
// Route::put('/ui-frontends/{ui-frontend}', [UiFrontendController::class, 'update'])->name('ui-frontends.update');
// Route::delete('/ui-frontends/{ui-frontend}', [UiFrontendController::class, 'destroy'])->name('ui-frontends.destroy');

Route::middleware(['web'])->group(function () {
  Route::group(['prefix' => LaravelLocalization::setLocale()], function()
  {
    $route_arguments = [
      'as' => 'ui-frontend::',
    ];

    Route::group($route_arguments, function () {
      Route::get('/ui-frontends', [UiFrontendController::class, 'index'])->name('index');

      // Account Routes
      Route::prefix('account')->name('account.')->group(function () {
        Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::get('/bookmarks', [AccountController::class, 'bookmarks'])->name('bookmarks');
        Route::get('/liked', [AccountController::class, 'liked'])->name('liked');
        Route::get('/completed', [AccountController::class, 'completed'])->name('completed');
        Route::get('/courses', [AccountController::class, 'courses'])->name('courses');
        Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
      });
    });
  });






});

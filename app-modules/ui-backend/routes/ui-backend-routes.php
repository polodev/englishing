<?php

// Route::get('/ui-backends', [UiBackendController::class, 'index'])->name('ui-backends.index');
// Route::get('/ui-backends/create', [UiBackendController::class, 'create'])->name('ui-backends.create');
// Route::post('/ui-backends', [UiBackendController::class, 'store'])->name('ui-backends.store');
// Route::get('/ui-backends/{ui-backend}', [UiBackendController::class, 'show'])->name('ui-backends.show');
// Route::get('/ui-backends/{ui-backend}/edit', [UiBackendController::class, 'edit'])->name('ui-backends.edit');
// Route::put('/ui-backends/{ui-backend}', [UiBackendController::class, 'update'])->name('ui-backends.update');
// Route::delete('/ui-backends/{ui-backend}', [UiBackendController::class, 'destroy'])->name('ui-backends.destroy');

use Illuminate\Support\Facades\Route;
use Modules\UiBackend\Http\Controllers\UiBackendController;

Route::middleware(['web'])->group(function () {
    $route_arguments = [
      'as' => 'ui-backend::',
    ];

    Route::group($route_arguments, function () {
        Route::get('/ui-backends', [UiBackendController::class, 'index'])->name('index');
    });
});
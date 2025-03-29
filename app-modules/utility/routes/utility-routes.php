<?php

// use Modules\Utility\Http\Controllers\UtilityController;

use Livewire\Volt\Volt;
Volt::route('/countertest', 'countertest');
Route::get('all-path', function () {
  return [
    'app_path' => app_path(),
    'base_path' => base_path(),
    'config_path' => config_path(),
    'database_path' => database_path(),
    'public_path' => public_path(),
    'resource_path' => resource_path(),
    'storage_path' => storage_path(),
  ];
});
Route::view('utility', 'utility::index');


// Route::get('/utilities', [UtilityController::class, 'index'])->name('utilities.index');
// Route::get('/utilities/create', [UtilityController::class, 'create'])->name('utilities.create');
// Route::post('/utilities', [UtilityController::class, 'store'])->name('utilities.store');
// Route::get('/utilities/{utility}', [UtilityController::class, 'show'])->name('utilities.show');
// Route::get('/utilities/{utility}/edit', [UtilityController::class, 'edit'])->name('utilities.edit');
// Route::put('/utilities/{utility}', [UtilityController::class, 'update'])->name('utilities.update');
// Route::delete('/utilities/{utility}', [UtilityController::class, 'destroy'])->name('utilities.destroy');

<?php

namespace Modules\Word\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Word\Http\Livewire\WordCreate;

class WordServiceProvider extends ServiceProvider
{
	public function register(): void
	{
	}
	
	public function boot(): void
	{
		// Register Livewire components
		Livewire::component('word-create', WordCreate::class);
		
		// Load views
		$this->loadViewsFrom(base_path('app-modules/word/resources/views'), 'word');
	}
}

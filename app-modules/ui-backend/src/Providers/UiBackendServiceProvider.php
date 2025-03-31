<?php

namespace Modules\UiBackend\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UiBackendServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		// Register any bindings or services here
	}
	
	public function boot(): void
	{
		$this->registerViews();
		$this->registerBladeComponents();
		$this->registerRoutes();
	}
	
	private function registerViews(): void
	{
		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'ui-backend');
	}
	
	private function registerBladeComponents(): void
	{
		Blade::componentNamespace('Modules\\UiBackend\\View\\Components', 'ui-backend');
	}
	
	private function registerRoutes(): void
	{
		Route::middleware('web')
			->group(function () {
				$this->loadRoutesFrom(__DIR__ . '/../../routes/ui-backend-routes.php');
			});
	}
}

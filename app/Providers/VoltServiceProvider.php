<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Volt::mount([
            config('livewire.view_path', resource_path('views/livewire')),
            resource_path('views/pages'),
             base_path('app-modules/user-usage-times/resources/views/livewire'),
             base_path('app-modules/word/resources/views/livewire'),
             base_path('app-modules/sentence/resources/views/livewire'),
             base_path('app-modules/expression/resources/views/livewire'),
             base_path('app-modules/article/resources/views/livewire'),
             base_path('app-modules/article-word/resources/views/livewire'),
             base_path('app-modules/article-sentence/resources/views/livewire'),
             base_path('app-modules/article-double-sentence/resources/views/livewire'),
             base_path('app-modules/article-expression/resources/views/livewire'),
             base_path('app-modules/article-conversation/resources/views/livewire'),
        ]);
    }
}

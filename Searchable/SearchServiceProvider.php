<?php

namespace Spatie\Searchable;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/skeleton.php' => config_path('skeleton.php'),
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'skeleton');

        $this->app->singleton(Search::class);
    }
}

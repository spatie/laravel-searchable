<?php

namespace Spatie\Searchable;

use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Search::class);
    }
}

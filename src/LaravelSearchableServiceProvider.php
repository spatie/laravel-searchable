<?php

namespace Spatie\Searchable;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\Searchable\Components\Search;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSearchableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-searchable')
            ->hasRoute('web')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViewComponent('search', Search::class)
            ->hasAssets();
    }

    public function packageBooted()
    {
        $this->registerBladeDirectives();
    }

    protected function registerBladeDirectives()
    {
        Blade::directive('searchStyles', function () {
            return "<link href='{{ asset('') }}' rel='stylesheet'>";
        });

        Blade::directive('searchScripts', function () {
            return "<script src='{{ asset('') }}'></script>";
        });
    }
}

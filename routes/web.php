<?php

use Illuminate\Support\Facades\Route;

Route::get(config('searchable.route.uri', 'search'), config('searchable.route.controller', \Spatie\Searchable\Controllers\SearchController::class))
    ->name(config('searchable.route.name', 'search'))
    ->middleware(config('searchable.route.middleware', ''));

<?php

use Spatie\Searchable\ModelSearchAspect;

return [

    'route' => [
        // The route assigned uri
        'uri' => 'search',

        // The route assigned name
        'name' => 'search',

        // The controller/method to use in Search request.
        'controller' => \Spatie\Searchable\Controllers\SearchController::class,

        // Any middleware for the search route group
        'middleware' => ['web'],
    ],

    'limit_aspect_results' => 10,

    'rule_validation' => ['required', 'min:5', 'string'],

    'models' => [
        // Name of modelClass => [...attributes]
        App\Models\User::class => [
            'name',
            'email'
        ],

        // Or Ex:

        // App\Models\User::class => function (ModelSearchAspect $modelSearchAspect) {
        //     $modelSearchAspect
        //         ->addSearchableAttribute('name') // return results for partial matches on usernames
        //         ->addExactSearchableAttribute('email') // only return results that exactly match the e-mail address
        //         ->active()
        //         ->has('posts')
        //         ->with('roles');
        // },
    ],
];

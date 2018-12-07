<?php

namespace Spatie\Searchable;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Searchable\Search
 */
class SearchFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Search::class;
    }
}

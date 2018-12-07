<?php

namespace Spatie\Searchable;

use Spatie\Searchable\SearchResult;

interface Searchable
{
    public function getSearchResult(): SearchResult;
}

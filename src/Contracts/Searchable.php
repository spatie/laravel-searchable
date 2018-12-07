<?php

namespace Spatie\Searchable\Contracts;

use Spatie\Searchable\Contracts\SearchResult as SearchResultInterface;

interface Searchable
{
    public function getSearchResult(): SearchResultInterface;
}

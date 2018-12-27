<?php

namespace Spatie\Searchable\Tests\stubs;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Account implements Searchable
{
    /** @var string */
    public $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult($this, $this->name);
    }
}

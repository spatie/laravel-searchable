<?php

namespace Spatie\Searchable\Tests\stubs;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchAspect;
use Spatie\Searchable\SearchResult;

class CustomNameSearchAspect extends SearchAspect
{
    protected $accounts = [];

    public function __construct()
    {
        $this->accounts = [
            new Account('john doe'),
            new Account('jane doe'),
            new Account('abc'),
        ];
    }

    public function getResults(string $term, ?User $user = null): Collection
    {
        return collect($this->accounts)
            ->filter(function (Account $account) use ($term) {
                return str_contains($account->name, $term);
            });
    }
}

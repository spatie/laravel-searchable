<?php

namespace Spatie\Searchable\Tests\stubs;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Spatie\Searchable\SearchAspect;

class CustomNameSearchAspect extends SearchAspect
{
    protected $data = [
        'john doe', 'jane doe', 'abc',
    ];

    public function getResults(string $term, ?User $user = null): Collection
    {
        return collect($this->data)
            ->filter(function (string $name) use ($term) {
                return str_contains($name, $term);
            });
    }
}

<?php

namespace Spatie\Searchable;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User;

abstract class SearchAspect
{
    abstract public function getResults(string $term, ?User $user = null): Collection;

    public function getType(): string
    {
        $className = class_basename(static::class);

        $type = str_before($className, 'SearchAspect');

        $type = snake_case(str_plural($type));

        return str_plural($type);
    }
}

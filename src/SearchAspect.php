<?php

namespace Spatie\Searchable;

use Illuminate\Support\Collection;

abstract class SearchAspect
{
    abstract public function getResults(string $term): Collection;

    public function getType(): string
    {
        if (isset(static::$searchType)) {
            return static::$searchType;
        }

        $className = class_basename(static::class);

        $type = str_before($className, 'SearchAspect');

        $type = snake_case(str_plural($type));

        return str_plural($type);
    }
}

<?php

namespace Spatie\Searchable;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

abstract class SearchAspect
{
    abstract public function getResults(string $query, User $user): Collection;

    public function getType(): string
    {
        $className = class_basename(static::class);

        $type = str_replace_last('SearchAspect', '', $className);

        $type = strtolower($type);

        return str_plural($type);
    }

    public function canBeUsedBy(User $user): bool
    {
        return true;
    }
}

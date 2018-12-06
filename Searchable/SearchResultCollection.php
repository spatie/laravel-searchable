<?php

namespace Spatie\Searchable;

use Illuminate\Support\Collection;

class SearchResultCollection extends Collection
{
    public function addResults(string $type, Collection $models)
    {
        $this->items[$type] = $models;

        return $this;
    }

    public function count()
    {
        return collect($this->items)->flatten()->count();
    }

    public function aspect(string $aspectName): Collection
    {
        return collect($this->items[$aspectName] ?? []);
    }
}

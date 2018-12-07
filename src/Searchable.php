<?php

namespace Spatie\Searchable;

interface Searchable
{
    public function getSearchResultName(): string;

    public function getSearchResultUrl(): string;
}

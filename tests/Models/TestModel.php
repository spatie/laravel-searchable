<?php

namespace Spatie\Searchable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\BasicSearchResult;
use Spatie\Searchable\Contracts\Searchable;
use Spatie\Searchable\Contracts\SearchResult;

class TestModel extends Model implements Searchable
{
    protected $guarded = [];

    public static function createWithName(string $name): self
    {
        return static::create([
            'name' => $name,
        ]);
    }

    public function getSearchResult(): SearchResult
    {
        return new BasicSearchResult();
    }
}

<?php

namespace Spatie\Searchable\Tests\Models;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model implements Searchable
{
    protected $guarded = [];

    public static function createWithName(string $name): self
    {
        return static::create([
            'name' => $name,
        ]);
    }

    public static function createWithNameAndLastName(string $name, $lastName): self
    {
        return static::create([
            'name' => $name,
            'last_name' => $lastName,
        ]);
    }

    public function getSearchResult(): SearchResult
    {
        return new SearchResult($this, $this->name);
    }
}

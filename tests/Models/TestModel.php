<?php

namespace Spatie\Searchable\Tests\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

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

    public function comments()
    {
        return $this->hasMany(TestComment::class);
    }

    public function scopeActive(Builder $query)
    {
        $query->where('active', 1);
    }
}

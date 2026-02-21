<?php

namespace Spatie\Searchable\Tests;

use PHPUnit\Framework\Attributes\Test;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class SearchResultTest extends TestCase
{
    #[Test]
    public function it_can_store_a_search_result()
    {
        $searchable = new class implements Searchable {
            public function getSearchResult(): SearchResult
            {
            }
        };

        $result = new SearchResult($searchable, 'Result', url('/'));

        $this->assertEquals($result->title, 'Result');
        $this->assertEquals($result->url, url('/'));
    }
}

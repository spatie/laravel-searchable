<?php

namespace Spatie\Searchable\Tests;

use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use PHPUnit\Framework\Attributes\Test;

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

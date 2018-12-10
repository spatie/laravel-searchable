<?php

namespace Spatie\Searchable\Tests;

use Spatie\Searchable\SearchResult;

class SearchResultTest extends TestCase
{
    /** @test */
    public function it_can_store_a_search_result()
    {
        $result = new SearchResult('Result', url('/'));

        $this->assertEquals($result->title(), 'Result');
        $this->assertEquals($result->url(), url('/'));
    }
}

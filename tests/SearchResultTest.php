<?php

namespace Spatie\Searchable\Tests;

use Spatie\Searchable\SearchResult;

class SearchResultTest extends TestCase
{
    /** @test */
    public function it_can_store_a_search_result()
    {
        $result = new SearchResult('Result', url('/'), 'Lorem ipsum', url('/'));

        $this->assertEquals($result->name(), 'Result');
        $this->assertEquals($result->description(), 'Lorem ipsum');
        $this->assertEquals($result->url(), url('/'));
        $this->assertEquals($result->imageUrl(), url('/'));
    }
}

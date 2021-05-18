<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Support\Arr;
use ReflectionObject;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;
use Spatie\Searchable\Tests\Models\TestModel;
use Spatie\Searchable\Tests\stubs\CustomNameSearchAspect;

class SearchTest extends TestCase
{
    /** @test */
    public function it_can_search_special_character()
    {
        TestModel::createWithName("alex%doe");
        TestModel::createWithName("alex_doe the second");
        TestModel::createWithName("_");
        TestModel::createWithName("%");
        TestModel::createWithName("jenna");

//        $search = new Search();

        $searchResults = (new \Spatie\Searchable\Search())
            ->registerModel(TestModel::class, "name")
            ->search("%");
        print_r($searchResults);
        print_r($searchResults->count());
        print_r(count($searchResults));
        $this->assertCount(2, $searchResults);
//        $search->registerModel(TestModel::class, 'name');
//        print_r(TestModel::query()->get()->toArray());
//        $results = $search->perform('%');
//        print_r($search->perform('%')->toArray());
//        print_r($results->count());
//        print_r(count($results));
//        $this->assertCount(2, $results);
//
//        $results = $search->perform('doe');
//        $this->assertCount(2, $results->count());
    }
}

<?php

namespace Spatie\Searchable\Tests;

use ReflectionObject;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Search;
use Spatie\Searchable\Tests\Models\TestModel;
use Spatie\Searchable\Tests\stubs\CustomNameSearchAspect;

class SearchTest extends TestCase
{
    /** @test */
    public function it_can_search_a_model_search_aspect()
    {
        TestModel::createWithName('john doe');
        TestModel::createWithName('alex');

        $search = new Search();

        $search->registerModel(TestModel::class, 'name');

        $results = $search->perform('doe');

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('test_models', $results);
        $this->assertCount(1, $results->aspect('test_models'));
    }

    /** @test */
    public function it_can_search_a_custom_search_aspect()
    {
        $search = new Search();

        $search->registerAspect(CustomNameSearchAspect::class);

        $results = $search->perform('doe');

        $this->assertCount(2, $results);
        $this->assertArrayHasKey('custom_names', $results);
        $this->assertCount(2, $results->aspect('custom_names'));
    }

    /** @test */
    public function it_can_search_multiple_aspects_together()
    {
        TestModel::createWithName('alex doe');
        TestModel::createWithName('jenna');

        $search = new Search();

        $search->registerAspect(CustomNameSearchAspect::class);
        $search->registerModel(TestModel::class, 'name');

        $results = $search->perform('doe');

        $this->assertCount(3, $results);
        $this->assertArrayHasKey('custom_names', $results);
        $this->assertArrayHasKey('test_models', $results);
        $this->assertCount(2, $results->aspect('custom_names'));
        $this->assertCount(1, $results->aspect('test_models'));
    }

    /** @test */
    public function it_can_register_a_class_name_as_search_aspect()
    {
        $search = (new Search())->registerAspect(CustomNameSearchAspect::class);

        $aspects = $search->getSearchAspects();

        $this->assertCount(1, $aspects);
        $this->assertInstanceOf(CustomNameSearchAspect::class, array_first($aspects));
    }

    /** @test */
    public function it_can_register_search_aspect()
    {
        $aspect = new CustomNameSearchAspect();

        $search = (new Search())->registerAspect($aspect);

        $aspects = $search->getSearchAspects();

        $this->assertCount(1, $aspects);
        $this->assertInstanceOf(CustomNameSearchAspect::class, array_first($aspects));
    }

    /** @test */
    public function it_can_register_a_model_search_aspect()
    {
        $search = new Search();

        $search->registerModel(TestModel::class);

        $aspects = $search->getSearchAspects();

        $this->assertCount(1, $aspects);
        $this->assertInstanceOf(ModelSearchAspect::class, array_first($aspects));
        $this->assertEquals('test_models', array_first($aspects)->getType());
    }

    /** @test */
    public function it_can_register_a_model_search_aspect_with_attributes()
    {
        $search = new Search();

        $search->registerModel(TestModel::class, 'name', 'email');

        $aspect = array_first($search->getSearchAspects());

        $refObject = new ReflectionObject($aspect);
        $refProperty = $refObject->getProperty('attributes');
        $refProperty->setAccessible(true);
        $attributes = $refProperty->getValue($aspect);

        $this->assertCount(2, $attributes);
    }
}

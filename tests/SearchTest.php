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
    public function it_can_search_a_model_search_aspect()
    {
        TestModel::createWithName('john doe');
        TestModel::createWithName('alex');

        $search = new Search();

        $search->registerModel(TestModel::class, 'name');

        $results = $search->perform('doe');

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('test_models', $results->groupByType());
        $this->assertCount(1, $results->aspect('test_models'));
    }

    /** @test */
    public function it_can_apply_scopes_and_eager_load_relationships_to_a_model_search_aspect()
    {
        $john = tap(TestModel::createWithName('john doe'), function ($model) {
            $model->update(['active' => true]);
            $model->comments()->create();
        });

        TestModel::createWithName('jane doe');
        TestModel::createWithName('john doe without comments');

        $search = new Search();

        $search->registerModel(TestModel::class, function (ModelSearchAspect $aspect) {
            $aspect->addSearchableAttribute('name')
                ->active()
                ->has('comments')
                ->with('comments');
        });

        $results = $search->perform('doe');

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('test_models', $results->groupByType());
        $this->assertCount(1, $results->aspect('test_models'));

        $searchableFound = $results->aspect('test_models')[0]->searchable;
        $this->assertTrue($searchableFound->is($john));
        $this->assertTrue($searchableFound->relationLoaded('comments'));
    }

    /** @test */
    public function it_can_register_a_model_search_aspect_attribute_thats_also_a_global_function()
    {
        $search = new Search();

        $search->registerModel(TestModel::class, 'phpinfo');

        $this->assertCount(1, $search->getSearchAspects());
    }

    /** @test */
    public function a_model_search_aspect_can_be_configured_using_a_closure()
    {
        TestModel::createWithName('john doe');
        TestModel::createWithName('alex');

        $search = new Search();

        $search->registerModel(TestModel::class, function (ModelSearchAspect $modelSearchAspect) {
            return $modelSearchAspect->addSearchableAttribute('name');
        });

        $results = $search->perform('doe');

        $this->assertCount(1, $results);
        $this->assertArrayHasKey('test_models', $results->groupByType());
        $this->assertCount(1, $results->aspect('test_models'));
    }

    /** @test */
    public function it_can_search_a_custom_search_aspect()
    {
        $search = new Search();

        $search->registerAspect(CustomNameSearchAspect::class);

        $results = $search->perform('doe');

        $this->assertCount(2, $results);
        $this->assertArrayHasKey('custom_names', $results->groupByType());
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
        $this->assertArrayHasKey('custom_names', $results->groupByType());
        $this->assertArrayHasKey('test_models', $results->groupByType());
        $this->assertCount(2, $results->aspect('custom_names'));
        $this->assertCount(1, $results->aspect('test_models'));
    }

    /** @test */
    public function it_can_register_a_class_name_as_search_aspect()
    {
        $search = (new Search())->registerAspect(CustomNameSearchAspect::class);

        $aspects = $search->getSearchAspects();

        $this->assertCount(1, $aspects);
        $this->assertInstanceOf(CustomNameSearchAspect::class, Arr::first($aspects));
    }

    /** @test */
    public function it_can_register_search_aspect()
    {
        $aspect = new CustomNameSearchAspect();

        $search = (new Search())->registerAspect($aspect);

        $aspects = $search->getSearchAspects();

        $this->assertCount(1, $aspects);
        $this->assertInstanceOf(CustomNameSearchAspect::class, Arr::first($aspects));
    }

    /** @test */
    public function it_can_register_a_model_search_aspect()
    {
        $search = new Search();

        $search->registerModel(TestModel::class);

        $aspects = $search->getSearchAspects();

        $this->assertCount(1, $aspects);
        $this->assertInstanceOf(ModelSearchAspect::class, Arr::first($aspects));
        $this->assertEquals('test_models', Arr::first($aspects)->getType());
    }

    /** @test */
    public function it_can_register_a_model_search_aspect_with_attributes()
    {
        $search = new Search();

        $search->registerModel(TestModel::class, 'name', 'email');

        $aspect = Arr::first($search->getSearchAspects());

        $refObject = new ReflectionObject($aspect);
        $refProperty = $refObject->getProperty('attributes');
        $refProperty->setAccessible(true);
        $attributes = $refProperty->getValue($aspect);

        $this->assertCount(2, $attributes);
    }

    /** @test */
    public function it_can_register_a_model_search_aspect_with_an_array_of_attributes()
    {
        $search = new Search();

        $search->registerModel(TestModel::class, ['name', 'email']);

        $aspect = Arr::first($search->getSearchAspects());

        $refObject = new ReflectionObject($aspect);
        $refProperty = $refObject->getProperty('attributes');
        $refProperty->setAccessible(true);
        $attributes = $refProperty->getValue($aspect);

        $this->assertCount(2, $attributes);
    }

    /** @test */
    public function it_can_register_a_model_search_aspect_with_a_attributes_from_a_callback()
    {
        $search = new Search();

        $search->registerModel(TestModel::class, function (ModelSearchAspect $modelSearchAspect) {
            $modelSearchAspect
                ->addSearchableAttribute('name')
                ->addExactSearchableAttribute('email');
        });

        $aspect = Arr::first($search->getSearchAspects());

        $refObject = new ReflectionObject($aspect);
        $refProperty = $refObject->getProperty('attributes');
        $refProperty->setAccessible(true);
        $attributes = $refProperty->getValue($aspect);

        $this->assertCount(2, $attributes);
    }

    /** @test */
    public function it_can_limit_aspect_results()
    {
        $search = new Search();

        TestModel::createWithName('Android 16');
        TestModel::createWithName('Android 17');
        TestModel::createWithName('Android 18');
        TestModel::createWithName('Android 19');
        TestModel::createWithName('Android 20');
        TestModel::createWithName('Android 21');

        $search->registerModel(TestModel::class, function (ModelSearchAspect $modelSearchAspect) {
            $modelSearchAspect
                ->addSearchableAttribute('name');
        });
        $results = $search->limitAspectResults(2)->perform('android');
        $this->assertCount(2, $results);
    }

    /** @test */
    public function it_can_limit_multiple_aspect_results()
    {
        $search = new Search();

        TestModel::createWithName('alex doe');
        TestModel::createWithName('alex doe the second');
        TestModel::createWithName('alex doe the third');
        TestModel::createWithName('alex doe the fourth');
        TestModel::createWithName('jenna');

        // This will return 2 as it's results are hard coded
        $search->registerAspect(CustomNameSearchAspect::class);
        // Our limiter should apply to the second aspect registered here and will make it return only 2
        $search->registerModel(TestModel::class, 'name');
        $results = $search->limitAspectResults(2)->perform('doe');
        $this->assertCount(4, $results);
    }

    /** @test */
    public function it_can_search_special_character()
    {
        TestModel::createWithName('alex%doe');
        TestModel::createWithName('alex_doe the second');
        TestModel::createWithName('_');
        TestModel::createWithName('%');
        TestModel::createWithName('jenna');

//        $search = new Search();

        $searchResults = (new \Spatie\Searchable\Search())
            ->registerModel(TestModel::class, 'name')
            ->search('%');
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

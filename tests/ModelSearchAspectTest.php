<?php

namespace Spatie\Searchable\Tests;

use ReflectionObject;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Tests\Models\TestModel;
use Spatie\Searchable\Exceptions\InvalidSearchableModel;
use Spatie\Searchable\Exceptions\InvalidModelSearchAspect;

class ModelSearchAspectTest extends TestCase
{
    /** @test */
    public function it_can_perform_a_search()
    {
        TestModel::createWithName('john');
        TestModel::createWithName('jane');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class, 'name');

        $results = $searchAspect->getResults('john');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(TestModel::class, $results[0]);
    }

    /** @test */
    public function it_can_add_searchable_attributes()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->addSearchableAttribute('email', false);

        $refObject = new ReflectionObject($searchAspect);
        $refProperty = $refObject->getProperty('attributes');
        $refProperty->setAccessible(true);
        $attributes = $refProperty->getValue($searchAspect);

        $this->assertTrue($attributes[0]->isPartial());
        $this->assertEquals('name', $attributes[0]->getAttribute());

        $this->assertFalse($attributes[1]->isPartial());
        $this->assertEquals('email', $attributes[1]->getAttribute());
    }

    /** @test */
    public function it_can_build_an_eloquent_query()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->addExactSearchableAttribute('email');

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_models" where LOWER(name) LIKE ? or "email" = ?';

        $executedQuery = array_get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    /** @test */
    public function it_has_a_type()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class);

        $this->assertEquals('test_models', $searchAspect->getType());
    }

    /** @test */
    public function it_throws_an_exception_when_given_a_class_that_is_not_a_model()
    {
        $notEvenAModel = new class {
        };

        $this->expectException(InvalidSearchableModel::class);

        ModelSearchAspect::forModel(get_class($notEvenAModel));
    }

    /** @test */
    public function it_throws_an_exception_when_given_an_unsearchable_model()
    {
        $modelWithoutSearchable = new class extends Model {
        };

        $this->expectException(InvalidSearchableModel::class);

        ModelSearchAspect::forModel(get_class($modelWithoutSearchable));
    }

    /** @test */
    public function it_throws_an_exception_if_there_are_no_searchable_attributes()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class);

        $this->expectException(InvalidModelSearchAspect::class);

        $searchAspect->getResults('john');
    }
}

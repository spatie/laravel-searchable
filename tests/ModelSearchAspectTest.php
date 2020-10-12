<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Grammar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use ReflectionObject;
use Spatie\Searchable\Exceptions\InvalidModelSearchAspect;
use Spatie\Searchable\Exceptions\InvalidSearchableModel;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Tests\Models\TestModel;

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
    public function it_can_perform_a_search_on_multiple_columns()
    {
        TestModel::createWithNameAndLastName('jane', 'doe');
        TestModel::createWithNameAndLastName('Taylor', 'Otwell');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class, 'name', 'last_name');

        $results = $searchAspect->getResults('Taylor Otwell');

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
        $searchableAttribute = 'name';
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute($searchableAttribute, true)
            ->addExactSearchableAttribute('email');
        /** @var Connection $connection */
        $connection = \DB::connection();
        /** @var Grammar $grammar */
        $grammar = $connection->getQueryGrammar();

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_models" where (LOWER(' . $grammar->wrap($searchableAttribute) . ') LIKE ? or "email" = ?)';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    /** @test */
    public function it_can_build_an_eloquent_query_with_segmented_values()
    {
        $searchableAttribute = 'test_models.name';
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute($searchableAttribute, true)
            ->addExactSearchableAttribute('email');
        /** @var Connection $connection */
        $connection = \DB::connection();
        /** @var Grammar $grammar */
        $grammar = $connection->getQueryGrammar();

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_models" where (LOWER(' . $grammar->wrap($searchableAttribute) . ') LIKE ? or "email" = ?)';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    /** @test */
    public function it_can_build_an_eloquent_query_to_eager_load_relationships()
    {
        $model = TestModel::createWithName('john');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->addExactSearchableAttribute('email')
            ->with('comments');

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_comments" where "test_comments"."test_model_id" in (' . $model->id . ')';

        $executedQuery = Arr::get(DB::getQueryLog(), '1.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    /** @test */
    public function it_can_build_an_eloquent_query_applying_scopes()
    {
        $searchableAttribute = 'name';
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute($searchableAttribute, true)
            ->active();
        /** @var Connection $connection */
        $connection = \DB::connection();
        /** @var Grammar $grammar */
        $grammar = $connection->getQueryGrammar();

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_models" where "active" = ? and (LOWER(' . $grammar->wrap($searchableAttribute) . ') LIKE ?)';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');
        $firstBinding = Arr::get(DB::getQueryLog(), '0.bindings.0');

        $this->assertEquals($expectedQuery, $executedQuery);
        $this->assertEquals(1, $firstBinding);
    }

    /** @test */
    public function it_can_build_an_eloquent_query_applying_scopes_with_same_keys()
    {
        $searchableAttribute = 'name';
        $firstBinding = 'blacklisted name';
        $secondBinding = 'blacklisted last name';
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute($searchableAttribute, true)
            ->whereNotIn('name', [$firstBinding])
            ->whereNotIn('last_name', [$secondBinding]);
        /** @var Connection $connection */
        $connection = \DB::connection();
        /** @var Grammar $grammar */
        $grammar = $connection->getQueryGrammar();

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_models" where "name" not in (?) and "last_name" not in (?) and (LOWER(' . $grammar->wrap($searchableAttribute) . ') LIKE ?)';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
        $this->assertEquals($firstBinding, Arr::get(DB::getQueryLog(), '0.bindings.0'));
        $this->assertEquals($secondBinding, Arr::get(DB::getQueryLog(), '0.bindings.1'));
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

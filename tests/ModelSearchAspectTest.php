<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use ReflectionObject;
use Spatie\Searchable\Exceptions\InvalidModelSearchAspect;
use Spatie\Searchable\Exceptions\InvalidSearchableModel;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Tests\Models\TestModel;
use PHPUnit\Framework\Attributes\Test;

class ModelSearchAspectTest extends TestCase
{
    #[Test]
    public function it_can_perform_a_search()
    {
        TestModel::createWithName('john');
        TestModel::createWithName('jane');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class, 'name');

        $results = $searchAspect->getResults('john');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(TestModel::class, $results[0]);
    }

    #[Test]
    public function it_can_perform_a_search_on_multiple_columns()
    {
        TestModel::createWithNameAndLastName('jane', 'doe');
        TestModel::createWithNameAndLastName('Taylor', 'Otwell');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class, 'name', 'last_name');

        $results = $searchAspect->getResults('Taylor Otwell');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(TestModel::class, $results[0]);
    }

    #[Test]
    public function it_can_perform_a_search_on_columns_with_reserved_name()
    {
        TestModel::createWithNameAndLastName('jane', 'doe');
        TestModel::createWithNameAndLastName('Taylor', 'Otwell');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class, 'name', 'where');

        $results = $searchAspect->getResults('Taylor Otwell');

        $this->assertCount(1, $results);
        $this->assertInstanceOf(TestModel::class, $results[0]);
    }

    #[Test]
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

    #[Test]
    public function it_can_build_an_eloquent_query()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->addExactSearchableAttribute('email');

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_models" where (LOWER("name") LIKE ? ESCAPE ? or "email" = ?)';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    #[Test]
    public function it_can_perform_an_exact_search_with_spaces_in_the_term()
    {
        TestModel::createWithName('have fun');
        TestModel::createWithName('other');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addExactSearchableAttribute('name');

        $results = $searchAspect->getResults('have fun');

        $this->assertCount(1, $results);
        $this->assertEquals('have fun', $results[0]->name);
    }

    #[Test]
    public function it_can_build_an_eloquent_query_to_eager_load_relationships()
    {
        $model = TestModel::createWithName('john');

        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->addExactSearchableAttribute('email')
            ->with('comments');

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_comments" where "test_comments"."test_model_id" in ('.$model->id.')';

        $executedQuery = Arr::get(DB::getQueryLog(), '1.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    #[Test]
    public function it_can_build_an_eloquent_query_applying_scopes()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->active();

        DB::enableQueryLog();

        $searchAspect->getResults('john');

        $expectedQuery = 'select * from "test_models" where (LOWER("name") LIKE ? ESCAPE ?) and "active" = ?';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');
        $secondBinding = Arr::get(DB::getQueryLog(), '0.bindings.2');

        $this->assertEquals($expectedQuery, $executedQuery);
        $this->assertEquals(1, $secondBinding);
    }

    #[Test]
    public function it_has_a_type()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class);

        $this->assertEquals('test_models', $searchAspect->getType());
    }

    #[Test]
    public function it_throws_an_exception_when_given_a_class_that_is_not_a_model()
    {
        $notEvenAModel = new class {
        };

        $this->expectException(InvalidSearchableModel::class);

        ModelSearchAspect::forModel(get_class($notEvenAModel));
    }

    #[Test]
    public function it_throws_an_exception_when_given_an_unsearchable_model()
    {
        $modelWithoutSearchable = new class extends Model {
        };

        $this->expectException(InvalidSearchableModel::class);

        ModelSearchAspect::forModel(get_class($modelWithoutSearchable));
    }

    #[Test]
    public function it_throws_an_exception_if_there_are_no_searchable_attributes()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class);

        $this->expectException(InvalidModelSearchAspect::class);

        $searchAspect->getResults('john');
    }

    #[Test]
    public function it_can_build_an_eloquent_query_by_many_same_methods()
    {
        TestModel::createWithNameAndLastNameAndGenderAndStatus('Taylor', 'Otwell', 'woman', true);

        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->where('gender', 'woman')
            ->where('status', 'activated');

        DB::enableQueryLog();

        $searchAspect->getResults('taylor');

        $expectedQuery = 'select * from "test_models" where (LOWER("name") LIKE ? ESCAPE ?) and "gender" = ? and "status" = ?';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    #[Test]
    public function it_can_build_an_eloquent_query_with_or_clause()
    {
        TestModel::createWithNameAndLastNameAndGenderAndStatus('Taylor', 'Otwell', 'woman', true);

        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->orWhere('gender', 'woman')
            ->orWhere('status', 'activated');

        DB::enableQueryLog();

        $searchAspect->getResults('woman');

        $expectedQuery = 'select * from "test_models" where (LOWER("name") LIKE ? ESCAPE ?) or "gender" = ? or "status" = ?';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }

    #[Test]
    public function it_can_build_an_eloquent_query_with_mixed_andor_clause()
    {
        TestModel::createWithNameAndLastNameAndGenderAndStatus('Taylor', 'Otwell', 'woman', true);

        $searchAspect = ModelSearchAspect::forModel(TestModel::class)
            ->addSearchableAttribute('name', true)
            ->orWhere(function ($query) {
                $query->where('gender', 'woman')
                    ->Where('status', 'activated');
            });

        DB::enableQueryLog();

        $searchAspect->getResults('woman');

        $expectedQuery = 'select * from "test_models" where (LOWER("name") LIKE ? ESCAPE ?) or ("gender" = ? and "status" = ?)';

        $executedQuery = Arr::get(DB::getQueryLog(), '0.query');

        $this->assertEquals($expectedQuery, $executedQuery);
    }
}

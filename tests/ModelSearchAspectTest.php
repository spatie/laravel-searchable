<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Exceptions\InvalidSearchableModelException;
use Spatie\Searchable\ModelSearchAspect;
use Spatie\Searchable\Tests\Models\TestModel;

class ModelSearchAspectTest extends TestCase
{
    /** @test */
    public function it_has_a_type()
    {
        $searchAspect = ModelSearchAspect::forModel(TestModel::class);

        $this->assertEquals('test_models', $searchAspect->getType());
    }

    /** @test */
    public function it_throws_an_exception_when_given_a_class_that_is_not_a_model()
    {
        $notEvenAModel = new class {};

        $this->expectException(InvalidSearchableModelException::class);

        ModelSearchAspect::forModel(get_class($notEvenAModel));
    }

    /** @test */
    public function it_throws_an_exception_when_given_an_unsearchable_model()
    {
        $modelWithoutSearchable = new class extends Model {};

        $this->expectException(InvalidSearchableModelException::class);

        ModelSearchAspect::forModel(get_class($modelWithoutSearchable));
    }
}

<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Database\Eloquent\Model;
use Spatie\Searchable\Exceptions\InvalidSearchableModelException;
use Spatie\Searchable\ModelSearchAspect;

class ModelSearchAspectTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_when_given_an_unsearchable_model()
    {
        $modelWithoutSearchable = new class extends Model {};

        $this->expectException(InvalidSearchableModelException::class);

        ModelSearchAspect::forModel($modelWithoutSearchable);
    }
}

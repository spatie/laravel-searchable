<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Foundation\Auth\User;
use Spatie\Searchable\Tests\stubs\CustomNameSearchAspect;

class SearchAspectTest extends TestCase
{
    /** @test */
    public function it_has_a_type()
    {
        $searchAspect = new CustomNameSearchAspect();

        $this->assertEquals('custom_names', $searchAspect->getType());
    }
}

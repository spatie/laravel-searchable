<?php

namespace Spatie\Searchable\Tests;

use Spatie\Searchable\Tests\stubs\CustomNameSearchAspect;
use PHPUnit\Framework\Attributes\Test;

class SearchAspectTest extends TestCase
{
    #[Test]
    public function it_has_a_type()
    {
        $searchAspect = new CustomNameSearchAspect();

        $this->assertEquals('custom_names', $searchAspect->getType());
    }

    #[Test]
    public function the_type_can_be_customized()
    {
        $searchAspectWithCustomName = new class extends CustomNameSearchAspect {
            public static $searchType = 'custom named aspect';
        };

        $this->assertEquals('custom named aspect', $searchAspectWithCustomName->getType());
    }
}

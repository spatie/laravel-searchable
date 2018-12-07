<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Searchable\SearchServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function setUpDatabase(Application $app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });
    }

    protected function getPackageProviders($app)
    {
        return [SearchServiceProvider::class];
    }
}

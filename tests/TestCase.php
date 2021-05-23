<?php

namespace Spatie\Searchable\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function setUpDatabase(Application $app)
    {
        Schema::dropAllTables();

        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('where')->nullable();
            $table->boolean('active')->default(false);
            $table->string('gender')->nullable();
        });

        Schema::create('test_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('test_model_id');
        });
    }

    protected function usesMySqlConnection(Application $app)
    {
        $app->config->set('database.default', 'mysql');
        $app->config->set('database.connections.mysql.database', 'test');
        $app->config->set('database.connections.mysql.username', 'root');
        $app->config->set('database.connections.mysql.password', '');
    }
}

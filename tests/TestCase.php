<?php

namespace Mehradsadeghi\CrudGenerator\Tests;

use Mehradsadeghi\CrudGenerator\Tests\Stubs\TestCrudGeneratorServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [TestCrudGeneratorServiceProvider::class];
    }
}
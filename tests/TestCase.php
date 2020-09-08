<?php

namespace Mehradsadeghi\CrudGenerator\Tests;


use Mehradsadeghi\CrudGenerator\CrudGeneratorServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [CrudGeneratorServiceProvider::class];
    }
}
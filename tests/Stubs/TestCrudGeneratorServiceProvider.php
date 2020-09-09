<?php

namespace Mehradsadeghi\CrudGenerator\Tests\Stubs;

use Mehradsadeghi\CrudGenerator\CrudGeneratorServiceProvider;

class TestCrudGeneratorServiceProvider extends CrudGeneratorServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([TestCrudGeneratorMakeCommand::class]);
        }
    }
}

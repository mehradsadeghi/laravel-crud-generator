<?php

namespace Mehradsadeghi\CrudGenerator;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider {

    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                CrudGeneratorMakeCommand::class,
                PublishCrudStubCommand::class
            ]);
        }
    }
}

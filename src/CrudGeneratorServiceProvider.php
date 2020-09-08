<?php

namespace Mehradsadeghi\CrudGenerator;

use App\Console\Commands\CrudGeneratorMakeCommand;
use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider {

    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([CrudGeneratorMakeCommand::class]);
        }
    }
}

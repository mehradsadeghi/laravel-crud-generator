<?php

namespace Mehradsadeghi\CrudGenerator\Tests\Stubs;

use Mehradsadeghi\CrudGenerator\CrudGeneratorMakeCommand;

class TestCrudGeneratorMakeCommand extends CrudGeneratorMakeCommand
{
    protected function getStub()
    {
        $model = $this->option('model');

        if ($model and $this->option('validation') and $this->modelHasFillables($model)) {
            return __DIR__.'/../../src/stubs/controller.model.validation.stub';
        } elseif ($model) {
            return __DIR__.'/../../src/stubs/controller.model.stub';
        } else {
            return __DIR__.'/../../src/stubs/controller.plain.stub';
        }
    }
}

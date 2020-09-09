<?php

namespace Mehradsadeghi\CrudGenerator\Tests\Stubs;

use Mehradsadeghi\CrudGenerator\CrudGeneratorMakeCommand;

class TestCrudGeneratorMakeCommand extends CrudGeneratorMakeCommand
{
    protected function getStub()
    {
        return str_replace(base_path(), __DIR__.'/../../src', parent::getStub());
    }
}

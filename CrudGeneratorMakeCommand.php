<?php

namespace App\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class CrudGeneratorMakeCommand extends ControllerMakeCommand
{
    protected $name = 'make:crud';
    protected $description = 'Create a controller with pre-defiend CRUD';
    protected $type = 'Controller';

    protected function buildModelReplacements(array $replace)
    {
        $model = parent::buildModelReplacements($replace);

        return [
            '{{ modelPluralVariable }}' => Str::plural(lcfirst(class_basename($this->option('model')))),
            '{{ resourcePluralVariable }}' => Str::plural(lcfirst(class_basename($this->option('model')))),
            '{{ namespace }}' => $this->getDefaultNamespace($this->getNamespace($this->rootNamespace())),
            '{{ namespacedModel }}' => $model['DummyFullModelClass'],
            '{{ rootNamespace }}' => $this->rootNamespace(),
            '{{ class }}' => $this->getNameInput(),
            '{{ model }}' => $model['DummyModelClass'],
            '{{ modelVariable }}' => $model['DummyModelVariable'],
        ];
    }

    protected function getStub()
    {
        if ($this->option('model')) {
            return base_path('stubs/controller.model.stub');
        }

        return base_path('stubs/controller.plain.stub');
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }
}

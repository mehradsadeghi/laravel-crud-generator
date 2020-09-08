<?php

namespace App\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudGeneratorMakeCommand extends ControllerMakeCommand
{
    protected $name = 'make:crud {--validation}';
    protected $description = 'Create a controller with pre-defiend CRUD';
    protected $type = 'Controller';

    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        if ($this->option('validation')) {
            $replace = $this->buildValidationReplacements($replace);
        }

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        $stub = $this->files->get($this->getStub());
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return str_replace(array_keys($replace), array_values($replace), $stub);
    }

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

    protected function buildValidationReplacements(array $replace)
    {
        $model = new $replace['{{ namespacedModel }}']();
        $fillables = $model->getFillable();
        $fillables = array_chunk($fillables, 1);

        $this->table([['fillables']], $fillables);

        $fillables = collect($fillables)->flatten()->toArray();

/*        $validations = <<<TEXT
TEXT;*/
        $validations = '';

        while($fillables) {
            $field = $this->anticipate('Enter Fillable', $fillables);

            $rules = $this->ask("Enter validation rules for $field field");
            $rules = str_replace(' ', '|', $rules);

            $validations .= "            \"$field\" => \"$rules\",\n";

            $fieldKey = array_keys($fillables, $field)[0];
            unset($fillables[$fieldKey]);
        }

        $validations = substr($validations, 0, -2);

        return array_merge($replace, [
            '{{ validations }}' => $validations
        ]);
    }

    protected function getStub()
    {
        if ($this->option('validation')) {
            return base_path('stubs/controller.model.validation.stub');
        }

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

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
            ['validation', null, InputOption::VALUE_NONE, 'Implement validation rules based on given model'],
        ];
    }
}

/*
 * $this->call('migrate');
        $table = Str::plural(lcfirst(class_basename($this->option('model'))));
        dd(Schema::hasTable($table));
        dd(Schema::getColumnListing($table));
 */

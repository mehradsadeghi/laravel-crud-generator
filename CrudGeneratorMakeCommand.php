<?php

namespace App\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudGeneratorMakeCommand extends ControllerMakeCommand
{
    protected $name = 'make:crud {--validation}';
    protected $description = 'Create a controller with pre-defiend CRUD and validation rules';
    protected $type = 'Controller';

    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($model = $this->option('model')) {

            $replace = $this->buildModelReplacements($replace);

            if ($this->option('validation') and $this->modelHasFillables($model)) {
                $replace = $this->buildValidationReplacements($replace);
            }
        }

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        $stub = $this->files->get($this->getStub());
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return str_replace(array_keys($replace), array_values($replace), $stub);
    }

    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return [
            '{{ modelPluralVariable }}' => Str::plural(lcfirst(class_basename($this->option('model')))),
            '{{ resourcePluralVariable }}' => Str::plural(lcfirst(class_basename($this->option('model')))),
            '{{ namespace }}' => $this->getDefaultNamespace($this->getNamespace($this->rootNamespace())),
            '{{ namespacedModel }}' => $modelClass,
            '{{ rootNamespace }}' => $this->rootNamespace(),
            '{{ class }}' => $this->getNameInput(),
            '{{ model }}' => class_basename($modelClass),
            '{{ modelVariable }}' => class_basename($modelClass),
        ];
    }

    protected function buildValidationReplacements(array $replace)
    {
        $model = new $replace['{{ namespacedModel }}']();

        if(!$fillables = $model->getFillable()) {
            return $replace;
        }

        $fillables = array_chunk($fillables, 1);

        $this->table([['fillables']], $fillables);

        $fillables = collect($fillables)->flatten()->toArray();

        $validations = '';

        while($fillables) {

            $field = $this->anticipate('Enter Fillable', $fillables);

            $rules = $this->ask("Enter validation rules for $field field");
            $rules = str_replace(' ', '|', $rules);

            $validations .= <<<TEXT
            "$field" => "$rules",\n
TEXT;

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
        if ($model = $this->option('model')) {

            if ($this->option('validation') and $this->modelHasFillables($model)) {
                return base_path('stubs/controller.model.validation.stub');
            }

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

    private function modelHasFillables($model)
    {
        if(File::exists(app_path($model)) and app($this->parseModel($model))->getFillable()) {
            return true;
        }

        return false;
    }
}

/*
 * $this->call('migrate');
        $table = Str::plural(lcfirst(class_basename($this->option('model'))));
        dd(Schema::hasTable($table));
        dd(Schema::getColumnListing($table));
 */

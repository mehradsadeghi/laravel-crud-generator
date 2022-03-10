<?php

namespace Mehradsadeghi\CrudGenerator;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Routing\Console\ControllerMakeCommand;

class CrudGeneratorMakeCommand extends ControllerMakeCommand {

    protected $name = 'make:crud';
    protected $description = 'Create a controller with pre-defiend CRUD and validation rules';
    protected $type = 'Controller';

    protected function buildClass($name) {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if($model = $this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        if($model and $this->option('validation')) {
            $replace = $this->buildValidationReplacements($replace, $this->getFillables($model));
        }

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        $stub = $this->files->get($this->getStub());
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return str_replace(array_keys($replace), array_values($replace), $stub);
    }

    protected function buildModelReplacements(array $replace) {
        $modelClass = $this->parseModel($this->option('model'));

        try {
            if (! class_exists($modelClass)) {
                if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                    $this->call('make:model', ['name' => $modelClass]);
                }
            }
        } catch (Exception $exception) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        $replacements = array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);

        return [
            '{{ modelPluralVariable }}' => Str::plural(lcfirst(class_basename($this->option('model')))),
            '{{ resourcePluralVariable }}' => Str::plural(lcfirst(class_basename($this->option('model')))),
            '{{ namespace }}' => $this->getDefaultNamespace($this->getNamespace($this->rootNamespace())),
            '{{ namespacedModel }}' => $replacements['DummyFullModelClass'],
            '{{ rootNamespace }}' => $this->rootNamespace(),
            '{{ class }}' => $this->getNameInput(),
            '{{ model }}' => $replacements['DummyModelClass'],
            '{{ modelVariable }}' => $replacements['DummyModelVariable'],
        ];
    }

    protected function buildValidationReplacements(array $replace, $fillables)
    {
        if (!$fillables) {
            return $replace;
        }

        $this->table([['fillables']], array_chunk($fillables, 1));
        $this->line('<fg=cyan;options=bold>>>></> Validation rules should be separated by <options=bold>white space</>.');
        $this->line('Example: required min:6 max:100</>');

        $validations = '';

        while ($fillables) {

            $field = $this->anticipate('Select Fillable By Arrow Keys or Typing It', $fillables);

            $rules = $this->ask("Enter validation rules for <fg=cyan;options=bold>$field</> field");

            $rules = str_replace(' ', '|', $rules);

            if($rules == '') {
                $this->line("<bg=red;options=bold>`$field` will be ignored from validations</>");
            } else {
                $validations .= <<<TEXT
            "$field" => "$rules",\n
TEXT;
            }

            $fillables = $this->unsetByValue($field, $fillables);
        }

        $validations = substr($validations, 0, -2);

        return array_merge($replace, [
            '{{ validations }}' => $validations
        ]);
    }

    protected function getStub() {

        $model = $this->option('model');

        $basePath = File::exists($this->laravel->basePath('stubs/crud'))
            ? $this->laravel->basePath('stubs/crud')
            : __DIR__.'/stubs';

        if($model and $this->option('validation') and $this->getFillables($model)) {
            $path = '/controller.model.validation.stub';
        } elseif($model) {
            $path = '/controller.model.stub';
        } else {
            $path = '/controller.plain.stub';
        }

        return $basePath.$path;
    }

    protected function getArguments() {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    protected function getOptions() {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.'],
            ['validation', null, InputOption::VALUE_NONE, 'Implement validation rules based on given model'],
        ];
    }

    private function modelHasFillables($model) {

        $path = File::exists(app_path('Models')) ? app_path('Models') : app_path();

        if(
            File::exists("$path/$model.php") and
            class_exists($this->parseModel($model)) and
            resolve($this->parseModel($model))->getFillable()
        ) {
            return true;
        }

        return false;
    }

    private function unsetByValue($field, array $fillables) {
        $fieldKey = array_search($field, $fillables);
        unset($fillables[$fieldKey]);
        return $fillables;
    }

    private function getFillables($model) {

        if($this->modelHasFillables($model)) {
            return resolve($this->parseModel($model))->getFillable();
        } elseif($this->isConnectedToDatabase() and [$table, $guarded] = $this->modelHasSchemaAndGuarded($model)) {
            $schema = Schema::getColumnListing($table);
            return array_diff($schema, $guarded);
        } else {
            return [];
        }
    }

    private function modelHasSchemaAndGuarded($model)
    {
        $table = Str::plural(lcfirst($model));

        if(!Schema::hasTable($table)) {
            return false;
        }

        $guarded = resolve($this->parseModel($model))->getGuarded();

        if($guarded[0] == '*') {
            return false;
        }

        return [$table, $guarded];
    }

    private function isConnectedToDatabase() {
        try {
            DB::connection()->getPdo();
        } catch (Exception $e) {
            return false;
        }
    }
}

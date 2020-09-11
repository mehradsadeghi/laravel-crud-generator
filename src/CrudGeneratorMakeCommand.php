<?php

namespace Mehradsadeghi\CrudGenerator;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CrudGeneratorMakeCommand extends ControllerMakeCommand
{
    protected $name = 'make:crud';
    protected $description = 'Create a controller with pre-defiend CRUD and validation rules';
    protected $type = 'Controller';

    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($model = $this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        if ($model and $this->option('validation') and $this->modelHasFillables($model)) {
            $replace = $this->buildValidationReplacements($replace, $replace['{{ namespacedModel }}']);
        }

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        $stub = $this->files->get($this->getStub());
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return str_replace(array_keys($replace), array_values($replace), $stub);
    }

    protected function buildModelReplacements(array $replace)
    {
        $replacements = parent::buildModelReplacements($replace);

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

    protected function buildValidationReplacements(array $replace, $model)
    {
        $fillables = resolve($model)->getFillable();

        $fillables = array_chunk($fillables, 1);

        $this->table([['fillables']], $fillables);

        $this->line('<fg=cyan;options=bold>>>></> Validation rules should be separated by <options=bold>white space</>.');
        $this->line('Example: required min:6 max:100</>');

        $fillables = collect($fillables)->flatten()->toArray();

        $validations = '';

        while($fillables) {

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

    protected function getStub()
    {
        $model = $this->option('model');

        if ($model and $this->option('validation') and $this->modelHasFillables($model)) {
            return base_path('stubs/controller.model.validation.stub');
        } elseif ($model) {
            return base_path('stubs/controller.model.stub');
        } else {
            return base_path('stubs/controller.plain.stub');
        }
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
        if(File::exists(app_path("$model.php")) and resolve($this->parseModel($model))->getFillable()) {
            return true;
        }

        return false;
    }

    private function unsetByValue($field, array $fillables)
    {
        $fieldKey = array_search($field, $fillables);
        unset($fillables[$fieldKey]);
        return $fillables;
    }
}

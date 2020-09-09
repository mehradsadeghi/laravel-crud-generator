<?php

namespace Mehradsadeghi\CrudGenerator\Tests;

use Illuminate\Console\Application;
use Illuminate\Support\Facades\File;
use Mehradsadeghi\CrudGenerator\Tests\Stubs\TestCrudGeneratorMakeCommand;
use Mehradsadeghi\CrudGenerator\Tests\Stubs\TestCrudGeneratorServiceProvider;
use Symfony\Component\Console\Tester\CommandTester;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $controller;
    protected $model;

    protected function getPackageProviders($app)
    {
        return [TestCrudGeneratorServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = app_path('Http/Controllers/UserController.php');
        $this->model = app_path('User.php');

        $this->deleteAppDirFiles();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->deleteAppDirFiles();
    }

    protected function runCommandWith(array $arguments = [], array $interactiveInput = []): CommandTester
    {
        $app = app(Application::class, ['version' => $this->app::VERSION]);
//        $app = (new Application(['version' => $this->app::VERSION]));
        $command = app(TestCrudGeneratorMakeCommand::class);

        $command->setLaravel($this->app);
        $command->setApplication($app);

        $tester = app(CommandTester::class, ['command' => $command]);
        $tester->setInputs($interactiveInput);

        $tester->execute($arguments);

        return $tester;
    }

    protected function deleteAppDirFiles()
    {
        File::cleanDirectory(app_path());
    }

    protected function getTestStub($path)
    {
        return __DIR__."/Stubs/$path";
    }
}
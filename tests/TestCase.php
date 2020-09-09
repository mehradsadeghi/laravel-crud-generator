<?php

namespace Mehradsadeghi\CrudGenerator\Tests;

use Illuminate\Support\Facades\File;
use Mehradsadeghi\CrudGenerator\Tests\Stubs\TestCrudGeneratorServiceProvider;

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

    protected function deleteAppDirFiles()
    {
        File::cleanDirectory(app_path());
    }

    protected function getLocalStub($path)
    {
        return __DIR__."/Stubs/$path";
    }
}
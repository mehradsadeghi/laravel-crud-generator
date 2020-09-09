<?php

namespace Mehradsadeghi\CrudGenerator\Tests;

use Illuminate\Support\Facades\File;

class CreateControllerTest extends TestCase
{
    private $controller;
    private $model;

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

    /** @test */
    public function empty_controller_file_can_be_created()
    {
        $this->assertFalse(File::exists($this->controller));

        $this->artisan('make:crud', ['name' => 'UserController'])
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));
    }

    /** @test */
    public function empty_controller_content_is_as_expected()
    {
        $this->assertFalse(File::exists($this->controller));

        $this->artisan('make:crud', ['name' => 'UserController'])
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));

        $this->assertEquals(
            File::get($this->controller),
            File::get(__DIR__.'/Stubs/Controllers/UserController.php')
        );
    }

    /** @test */
    public function controller_file_with_model_can_be_created()
    {
        $this->assertFalse(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->artisan('make:crud', ['name' => 'UserController', '--model' => 'User'])
            ->expectsQuestion("A App\User model does not exist. Do you want to generate it?", 'yes')
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));
        $this->assertTrue(File::exists($this->model));
    }

    private function deleteAppDirFiles()
    {
        File::cleanDirectory(app_path());
    }
}
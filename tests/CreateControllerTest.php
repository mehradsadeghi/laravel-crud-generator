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
            File::get($this->getLocalStub('Controllers/EmptyUserController.php'))
        );
    }

    /** @test */
    public function controller_file_with_model_can_be_created()
    {
        $this->assertFalse(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->artisan('make:crud', ['name' => 'UserController', '--model' => 'User'])
            ->expectsQuestion("A App\User model does not exist. Do you want to generate it?", true)
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));
        $this->assertTrue(File::exists($this->model));

        $this->deleteAppDirFiles();

        $this->artisan('make:crud', ['name' => 'UserController', '--model' => 'User'])
            ->expectsQuestion("A App\User model does not exist. Do you want to generate it?", false)
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));
    }

    /** @test */
    public function controller_content_with_model_is_as_expected()
    {
        $this->assertFalse(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->artisan('make:crud', ['name' => 'UserController', '--model' => 'User'])
            ->expectsQuestion("A App\User model does not exist. Do you want to generate it?", true)
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));
        $this->assertTrue(File::exists($this->model));

        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getLocalStub('Controllers/UserControllerWithModel.php'))
        );

        $this->deleteAppDirFiles();

        $this->artisan('make:crud', ['name' => 'UserController', '--model' => 'User'])
            ->expectsQuestion("A App\User model does not exist. Do you want to generate it?", false)
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getLocalStub('Controllers/UserControllerWithModel.php'))
        );
    }

    private function deleteAppDirFiles()
    {
        File::cleanDirectory(app_path());
    }

    private function getLocalStub($path)
    {
        return __DIR__."/Stubs/$path";
    }
}
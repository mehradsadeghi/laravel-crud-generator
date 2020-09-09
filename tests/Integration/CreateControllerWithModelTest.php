<?php

namespace Mehradsadeghi\CrudGenerator\Tests\Integration;

use Illuminate\Support\Facades\File;
use Mehradsadeghi\CrudGenerator\Tests\TestCase;

class CreateControllerWithModelTest extends TestCase
{
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
}
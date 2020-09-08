<?php

namespace Mehradsadeghi\CrudGenerator\Tests;

use App\UserWithFillable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
//        $this->deleteAppDirFiles();
    }

    /** @test */
    public function controller_file_with_model_can_be_created()
    {
        $this->assertFalse(File::exists($this->controller));

        $this->artisan('make:crud UserController --model=User')
            ->expectsQuestion("A App\User model does not exist. Do you want to generate it?", 'yes')
            ->assertExitCode(0);

        $this->assertTrue(File::exists($this->controller));
        $this->assertTrue(File::exists($this->model));
    }

    /** @test */
    public function sample()
    {
        $this->loadMigrationsFrom(base_path('migrations'));
        File::copy(__DIR__.'/stubs/models/UserWithFillable.php', app_path().'/UserWithFillable.php');

        $columns = Schema::getColumnListing('users');
    }

    private function deleteAppDirFiles()
    {
        File::cleanDirectory(app_path());
    }
}

// migration =>
// fillable => (new User)->getFillable()
// guarded => (new User)->getGuarded()
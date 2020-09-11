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

        $this->runCommandWith(['name' => 'UserController', '--model' => 'User'], ['yes']);

        $this->assertTrue(File::exists($this->controller));
        $this->assertTrue(File::exists($this->model));

        $this->deleteAppDirFiles();

        $this->runCommandWith(['name' => 'UserController', '--model' => 'User'], ['no']);

        $this->assertTrue(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));
    }

    /** @test */
    public function controller_content_with_model_is_as_expected()
    {
        $this->assertFalse(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->runCommandWith(['name' => 'UserController', '--model' => 'User'], ['yes']);

        $this->assertTrue(File::exists($this->controller));
        $this->assertTrue(File::exists($this->model));

        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getTestStub('Controllers/UserControllerWithModel.php'))
        );

        $this->deleteAppDirFiles();

        $this->runCommandWith(['name' => 'UserController', '--model' => 'User'], ['no']);

        $this->assertTrue(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getTestStub('Controllers/UserControllerWithModel.php'))
        );
    }
}
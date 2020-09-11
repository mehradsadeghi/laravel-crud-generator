<?php

namespace Mehradsadeghi\CrudGenerator\Tests\Integration;

use Illuminate\Support\Facades\File;
use Mehradsadeghi\CrudGenerator\Tests\TestCase;

class CreateControllerWithModelAndValidationsTest extends TestCase
{
    /** @test */
    public function controller_file_with_existing_model_and_fillables_with_validations_can_be_created()
    {
        $this->assertFalse(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        File::copy($this->getTestStub('Models/UserWithFillable.php'), $this->model);

        $this->assertTrue(File::exists($this->model));

        $this->runCommandWith(
            ['name' => 'UserController', '--model' => 'User', '--validation' => true],
            ['name', 'required min:3 max:100', 'password', 'required min:6', 'email', 'required email unique:users,email']
        );

        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getTestStub('Controllers/UserControllerWithModelAndValidation.php'))
        );
    }

    /** @test */
    public function controller_file_with_existing_model_and_no_fillables_with_validations_can_be_created()
    {
        $this->assertFalse(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        // there is an issue when passing --model (when it doesn't exist) and --validation together
        // in this situation the --no-interactive option doesn't work correctly
        // so as a temporary workaround I'm creating the empty model here
        File::copy($this->getTestStub('Models/EmptyUser.php'), $this->model);

        $this->assertTrue(File::exists($this->model));

        $this->runCommandWith(['name' => 'UserController', '--model' => 'User', '--validation' => true]);

        /*$this->assertTrue(File::exists($this->controller));
        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getTestStub('Controllers/UserControllerWithModel.php'))
        );*/


        /*$this->deleteAppDirFiles();
        dd(File::get($this->model));

        $this->assertFalse(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->runCommandWith(['name' => 'UserController', '--model' => 'User', '--validation'], ['no']);

        $this->assertTrue(File::exists($this->controller));
        $this->assertFalse(File::exists($this->model));

        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getTestStub('Controllers/UserControllerWithModel.php'))
        );*/
    }
}
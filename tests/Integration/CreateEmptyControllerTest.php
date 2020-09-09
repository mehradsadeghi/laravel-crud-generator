<?php

namespace Mehradsadeghi\CrudGenerator\Tests\Integration;

use Illuminate\Support\Facades\File;
use Mehradsadeghi\CrudGenerator\Tests\TestCase;

class CreateEmptyControllerTest extends TestCase
{
    /** @test */
    public function empty_controller_file_can_be_created()
    {
        $this->assertFalse(File::exists($this->controller));

        $this->runCommandWith(['name' => 'UserController']);

        $this->assertTrue(File::exists($this->controller));
    }

    /** @test */
    public function empty_controller_content_is_as_expected()
    {
        $this->assertFalse(File::exists($this->controller));

        $this->runCommandWith(['name' => 'UserController']);

        $this->assertTrue(File::exists($this->controller));

        $this->assertEquals(
            File::get($this->controller),
            File::get($this->getTestStub('Controllers/EmptyUserController.php'))
        );
    }
}
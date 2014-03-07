<?php

use \Mockery;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Impleri\Resource\ControllerGenerator;
use Impleri\Resource\Facades\ControllerGenerator as ControllerGeneratorFacade;

class GenerateControllersTest extends PHPUnit_Framework_TestCase
{
    protected static $output = 'test success';

    protected static $params = [
        'classes' => [
            [
                'name' => 'test'
            ]
        ]
    ];

    public function setUp()
    {
        $this->unit = new ControllerGenerator;
    }

    /**
     * Tear Down
     *
     * Clean up any mocked objects we created.
     */
    public function tearDown()
    {
        Mockery::close();
    }

    public function testFacadeResponds()
    {
        ControllerGeneratorFacade::shouldReceive('execute')
            ->andReturn(true);
        ControllerGeneratorFacade::execute();
    }

    /**
     * Routes Have Valid Parameters
     *
     * Ensure BuildCommand fires correctly.
     * @expectedException BadFunctionCallException
     */
    public function testControllersValidatesInput()
    {
        $this->unit->execute([]);
    }

    public function testControllersAreGenerated()
    {
        View::shouldReceive('make->render')
            ->andReturn(static::$output);
        
        File::shouldReceive('exists')
            ->with('/\.bak\.php$/')
            ->andReturn(false);

        File::shouldReceive('exists')
            ->with('/\.php$/')
            ->andReturn(false);

        File::shouldReceive('put')
            ->with('/\.php$/', static::$output)
            ->andReturn(true);

        $this->unit->execute(static::$params);
    }
}

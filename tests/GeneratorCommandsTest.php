<?php

use \Mockery;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;

class GeneratorCommandsTest extends PHPUnit_Framework_TestCase
{
    protected static $routes_output = 'test success';

    protected static $routes_parameters = ['element' => 'test'];

    protected static $app = null;

    /**
     * Mock the application
     * @return \Mockery\expectation [description]
     */
    public function setUp()
    {
        // Saves the app for ultimate restoration
        self::$app = app();
    }

    /**
     * Mock the application
     * @return \Mockery\expectation [description]
     */
    public function routesSetUp()
    {
        // Stubs the render response
        $message = self::$routes_output;
        $made = Mockery::mock('made', function ($mock) use ($message) {
            $mock->shouldReceive('render')
                ->andReturn($message);
        });

        // Stubs the make response
        $params = self::$routes_parameters;
        $view = Mockery::mock('view', function ($mock) use ($made, $params) {
            // Make sure the generator passes the correct data
            $mock->shouldReceive('make')
                ->with('resource::routes', Mockery::subset($params))
                ->andReturn($made);

        });

        // Replaces the app
        Facade::setFacadeApplication(['view' => $view]);
    }

    /**
     * Build Command Test
     *
     * Ensure BuildCommand fires correctly.
     */
    public function testRoutesValidatesInput()
    {
        $this->routesSetUp();
        $this->setExpectedException('BadFunctionCallException');
        Impleri\Resource\Generator::routes([]);
    }

    /**
     * Build Command Test
     *
     * Ensure BuildCommand fires correctly.
     */
    public function testRoutesAreGenerated()
    {
        $this->routesSetUp();
        $result = Impleri\Resource\Generator::routes(self::$routes_parameters);
        $this->assertEquals($result, self::$routes_output);
    }

    /**
     * Tear Down
     *
     * Clean up any mocked objects we created.
     */
    public function tearDown()
    {
        Mockery::close();

        // Restore the app for others
        Facade::setFacadeApplication(self::$app);
    }
}

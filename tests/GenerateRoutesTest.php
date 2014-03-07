<?php

use \Mockery;
use Illuminate\Support\Facades\View;
use Impleri\Resource\RouteGenerator;
use Impleri\Resource\Facades\RouteGenerator as RouteGeneratorFacade;

class GenerateRoutesTest extends PHPUnit_Framework_TestCase
{
    protected static $output = 'test success';

    protected static $params = [
        'element' => 'test'
    ];

    public function setUp()
    {
        $this->unit = new RouteGenerator;
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
        RouteGeneratorFacade::shouldReceive('execute')
            ->andReturn(true);
        RouteGeneratorFacade::execute();
    }

    /**
     * Routes Have Valid Parameters
     *
     * Ensure BuildCommand fires correctly.
     * @expectedException BadFunctionCallException
     */
    public function testRoutesValidatesInput()
    {
        $this->unit->execute([]);
    }

    /**
     * Routes Are Created
     *
     * Ensure BuildCommand fires correctly.
     */
    public function testRoutesAreGenerated()
    {
        View::shouldReceive('make->render')
            ->andReturn(static::$output);

        $result = $this->unit->execute(self::$params);

        $this->assertEquals($result, self::$output);
    }
}

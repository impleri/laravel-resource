<?php

use \Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class GenerateRoutesTest extends PHPUnit_Framework_TestCase
{
    protected static $output = 'test success';

    protected static $params = ['element' => 'test'];

    /**
     * Tear Down
     *
     * Clean up any mocked objects we created.
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Routes Have Valid Parameters
     *
     * Ensure BuildCommand fires correctly.
     */
    public function testRoutesValidatesInput()
    {
        $this->setExpectedException('BadFunctionCallException');
        Impleri\Resource\Generator::routes([]);
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
        $result = Impleri\Resource\Generator::routes(self::$params);
        $this->assertEquals($result, self::$output);
    }
}

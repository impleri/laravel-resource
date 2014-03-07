<?php

use \Mockery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

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
    public function testControllersValidatesInput()
    {
        $this->setExpectedException('BadFunctionCallException');
        Impleri\Resource\Generator::controllers([]);
    }

    public function testControllersAreGenerated()
    {
        File::shouldReceive('exists')
            ->with('/\.php$/')
            ->andReturn(false);
        File::shouldReceive('put')
            ->with('/\.php$/', Mockery::type('string'))
            ->andReturn(true);
        View::shouldReceive('make->render')
            ->andReturn(static::$output);
        Impleri\Resource\Generator::controllers(static::$params);
    }
}

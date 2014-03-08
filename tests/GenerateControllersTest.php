<?php

use \Mockery;
use Codeception\Specify;
use Codeception\Verify;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Impleri\Resource\ControllerGenerator;
use Impleri\Resource\Facades\ControllerGenerator as ControllerGeneratorFacade;

class GenerateControllersTest extends PHPUnit_Framework_TestCase
{
    use Specify;

    protected static $output = 'test success';

    protected static $params = [
        'classes' => [
            [
                'name' => 'test'
            ]
        ]
    ];

    protected static function getMethod ($name)
    {
        $class = new ReflectionClass('Impleri\Resource\ControllerGenerator');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Set up
     */
    public function setUp()
    {
        $this->unit = new ControllerGenerator;
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * Does the facade respond to execute?
     */
    public function testFacade()
    {
        $this->specify('responds to execute command', function () {
            // Fake the response
            ControllerGeneratorFacade::shouldReceive('execute')
                ->andReturn(true);

            // This should match the mocked response
            expect(ControllerGeneratorFacade::execute())
                ->equals(true);
        });
    }

    /**
     * Tests for the generator save method
     */
    public function testSaveMethod()
    {
        $save = self::getMethod('save');
        $class = new Impleri\Resource\ControllerGenerator;

        $this->specify('manages backups.', function () use ($save, $class) {
            // If the backup file exists ...
            File::shouldReceive('exists')
                ->with('saves.bak.php')
                ->andReturn(true);

            // ... it should be deleted
            File::shouldReceive('delete')
                ->with('saves.bak.php');

            // If the file exists ...
            File::shouldReceive('exists')
                ->with('saves.php')
                ->andReturn(false);

            // ... it should be moved
            File::shouldReceive('move')
                ->with('saves.php', 'saves.bak.php');

            // If the file is saved ...
            File::shouldReceive('put')
                ->with('saves.php', 'contents')
                ->andReturn(true);

            // ... save() should return true
            expect($save->invoke($class, 'saves', 'contents'))->true();
        });

        $this->specify('notifies when save failed.', function () use ($save, $class) {
            // Assume files don't exist here
            File::shouldReceive('exists')
                ->with('/\.php$/')
                ->andReturn(false);

            // If the file doesn't save ...
            File::shouldReceive('put')
                ->with('fails.php', 'contents')
                ->andReturn(false);

            // ... save() should return false
            expect($save->invoke($class, 'fails', 'contents'))->false();
        });
    }

    /**
     * Tests for the generator fillOptions method
     */
    public function testFillOptionsMethod()
    {
        $fill = self::getMethod('fillOptions');
        $class = new Impleri\Resource\ControllerGenerator;

        $this->specify(
            'throws exception if array does not have classes.',
            function () use ($class, $fill) {
                $options = [];
                $options = $fill->invoke($class, $options);
            },
            ['throws' => 'BadFunctionCallException']
        );

        $this->specify('provides default values.', function () use ($class, $fill) {
            $options = [
                'classes' => [
                    'test' => []
                ]
            ];

            $options = $fill->invoke($class, $options);

            expect($options['baseNamespace'])->isEmpty();

            expect($options['basePath'])->equals('app/controllers/resources/');
        });

        $this->specify('keeps given values.', function () use ($class, $fill) {
            $options = [
                'baseNamespace' => 'test\\',
                'basePath' => 'path/',
                'classes' => [
                    'test' => []
                ]
            ];

            $options = $fill->invoke($class, $options);

            expect($options['baseNamespace'])->equals('test\\');

            expect($options['basePath'])->equals('path/test/');
        });
    }

    /**
     * Tests for the generator fillOptions method
     */
    public function testFillClassMethod()
    {
        $fill = self::getMethod('fillClass');
        $class = new Impleri\Resource\ControllerGenerator;

        $options = [
                'baseNamespace' => 'test\\',
                'basePath' => 'path/test/',
                'classes' => [
                    'test' => []
                ]
            ];

        $this->specify(
            'throws exception if array does not have classes.',
            function () use ($class, $fill, $options) {
                $class = [];
                $data = $fill->invoke(0, [], $options);
            },
            ['throws' => 'BadFunctionCallException']
        );

        $this->specify('provides default values.', function () use ($class, $fill, $options) {
            $data = $fill->invoke($class, 'class', [], $options);

            expect($data['namespace'])->equals('test\\');

            expect($data['class'])->equals('class');

            expect($data['file'])->equals('path/test/class');
        });

        // $this->specify('keeps given values.', function () use ($class, $fill) {
        //     $options = [
        //         'baseNamespace' => 'test\\',
        //         'basePath' => 'path/',
        //         'classes' => [
        //             'test' => []
        //         ]
        //     ];

        //     $options = $fill->invoke($class, $options);

        //     expect($options['baseNamespace'])->equals('test\\');

        //     expect($options['basePath'])->equals('path/test/');
        // });
    }

    /**
     * Does it render
     */
    public function testExecuteMethod()
    {
        $this->specify('renders if one class name is passed.', function () {
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

            expect($this->unit->execute(static::$params))->greaterThan(0);
        });
    }
}

<?php

use \Mockery;
use Codeception\Specify;
use Codeception\Verify;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Impleri\Resource\Generator;

/**
 * Child class of Impleri\Resource\Generator for
 * testing.
 */
class ConcreteGenerator extends Generator
{
    // Nothing to see here.
    // Really. Nothing.
}

/**
 * Specs for Impleri\Resource\Generator.
 */
class GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Consume Codeception's Specify Trait
     */
    use Specify;

    /**
     * Does the controller facade respond?
     */
    public function testControllerFacade()
    {
        $this->prepareSpecify();

        $this->specify('responds to execute command', function () {
            // Fake the response
            Impleri\Resource\Facades\ControllerGenerator::shouldReceive('execute')
                ->andReturn(true);

            // This should match the mocked response
            expect(Impleri\Resource\Facades\ControllerGenerator::execute())
                ->true();
        });
    }

    /**
     * Specs for Generator::trailingSeparator()
     */
    public function testTrailingSeparatorMethod()
    {
        $trail = $this->getMethod('trailingSeparator');
        $class = $this->prepareSpecify();

        $this->specify('adds a separtor.', function () use ($trail, $class) {
            expect($trail->invoke($class, 'test'))->equals('test/');
        });

        $this->specify('does not duplicate a separtor.', function () use ($trail, $class) {
            expect($trail->invoke($class, 'test/'))->equals('test/');
        });

        $this->specify('uses a provided separtor.', function () use ($trail, $class) {
            expect($trail->invoke($class, 'test', '\\'))->equals('test\\');
        });
    }

    /**
     * Specs for Generator::convertNamespace()
     */
    public function testConvertNamespaceMethod()
    {
        $convert = $this->getMethod('convertNamespace');
        $class = $this->prepareSpecify();

        $this->specify('converts slashes.', function () use ($convert, $class) {
            expect($convert->invoke($class, 'Test\\Case'))->equals('Test/Case');
        });
    }

    /**
     * Specs for Generator::save()
     */
    public function testSaveMethod()
    {
        $save = $this->getMethod('save');
        $class = $this->prepareSpecify();

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
     * Specs for Generator::fillOptions()
     */
    public function testFillOptionsMethod()
    {
        $fill = $this->getMethod('fillOptions');
        $class = $this->prepareSpecify();

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

            expect($options['basePath'])->equals('resources/');
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
     * Specs for Generator::fillOptions()
     */
    public function testFillClassMethod()
    {
        $fill = $this->getMethod('fillClass');
        $class = $this->prepareSpecify();

        $options = [
                'baseNamespace' => 'test',
                'basePath' => 'path/test',
                'classes' => [
                    'test' => []
                ]
            ];

        $this->specify(
            'throws exception if array does not have classes.',
            function () use ($class, $fill, $options) {
                $data = $fill->invoke($class, 0, [], $options);
            },
            ['throws' => 'BadFunctionCallException']
        );

        $this->specify('provides default values.', function () use ($class, $fill, $options) {
            $data = $fill->invoke($class, 'class', [], $options);

            expect($data['namespace'])->equals('test');

            expect($data['class'])->equals('class');

            expect($data['file'])->equals('path/test/class');
        });

        $this->specify('keeps given values.', function () use ($class, $fill, $options) {
            $item = [
                'subNamespace' => 'subtest',
                'name' => 'Subclass',
                'classes' => [
                    'test' => []
                ]
            ];

            $data = $fill->invoke($class, 'class', $item, $options);

            expect($data['namespace'])->equals('test\\subtest');

            expect($data['class'])->equals('Subclass');

            expect($data['file'])->equals('path/test/subtest/Subclass');
        });
    }

    /**
     * Specs for Generator::execute()
     */
    public function testExecuteMethod()
    {
        $class = $this->prepareSpecify();

        $this->specify('renders if one class name is passed.', function () use ($class) {
            View::shouldReceive('make->render')
                ->once()
                ->andReturn('test output');

            File::shouldReceive('exists')
                ->twice()
                ->with('/\.php$/')
                ->andReturn(false);

            File::shouldReceive('put')
                ->once()
                ->with('/\.php$/', 'test output')
                ->andReturn(true);

            $params = [
                'classes' => [
                    'test' => []
                ]
            ];
            expect($class->execute($params))->greaterThan(0);
        });

        $this->specify('does not count failed views.', function () use ($class) {
            View::shouldReceive('make->render')
                ->once()
                ->andReturn('test output');

            File::shouldReceive('exists')
                ->twice()
                ->with('/\.php$/')
                ->andReturn(false);

            File::shouldReceive('put')
                ->once()
                ->with('/\.php$/', 'test output')
                ->andReturn(false);

            $params = [
                'classes' => [
                    'test' => []
                ]
            ];
            expect($class->execute($params))->equals(0);
        });

        $this->specify('keeps overridden view.', function () use ($class) {
            View::shouldReceive('make')
                ->once()
                ->with('test::view', Mockery::type('array'));

            View::shouldReceive('make->render')
                ->once()
                ->andReturn('test output');

            File::shouldReceive('exists')
                ->twice()
                ->with('/\.php$/')
                ->andReturn(false);

            File::shouldReceive('put')
                ->once()
                ->with('/\.php$/', 'test output')
                ->andReturn(true);

            $params = [
                'classes' => [
                    'test' => []
                ]
            ];
            expect($class->execute($params))->greaterThan(0);
        });
    }

    /**
     * Get (Protected) Method
     *
     * Uses Reflection to get a method for
     * testing
     * @param  string $name Method name to retrieve
     * @return ReflectionMethod Class Method
     */
    protected function getMethod ($name)
    {
        $class = new ReflectionClass('ConcreteGenerator');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Set up for each spec
     *
     * @return  ConcreteGenerator De-abstracted Generator class
     */
    protected function prepareSpecify()
    {
        $this->cleanSpecify();
        $this->afterSpecify(function () {
            Mockery::close();
        });

        return new ConcreteGenerator;
    }
}

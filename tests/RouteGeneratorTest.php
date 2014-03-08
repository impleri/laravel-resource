<?php

use \Mockery;
use Codeception\Specify;
use Codeception\Verify;
use Illuminate\Support\Facades\View;
use Impleri\Resource\RouteGenerator;

/**
 * Specs for Impleri\Resource\RouteGenerator.
 */
class RouteGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Consume Codeception's Specify Trait
     */
    use Specify;

    /**
     * Does the facade respond?
     */
    public function testFacade()
    {
        $this->prepareSpecify();

        $this->specify('responds to execute command', function () {
            // Fake the response
            Impleri\Resource\Facades\RouteGenerator::shouldReceive('execute')
                ->andReturn(true);

            // This should match the mocked response
            expect(Impleri\Resource\Facades\RouteGenerator::execute())
                ->equals(true);
        });
    }

    /**
     * Specs for RouteGenerator::fillOptions()
     */
    public function testFillOptionsMethod()
    {
        $fill = $this->getMethod('fillOptions');
        $class = $this->prepareSpecify();

        $this->specify(
            'throws exception if array does not have either an element or a collection.',
            function () use ($class, $fill) {
                $options = [];
                $options = $fill->invoke($class, $options);
            },
            ['throws' => 'BadFunctionCallException']
        );

        $this->specify('provides default value from element.', function () use ($class, $fill) {
            $options = [
                'element' => 'test'
            ];

            $options = $fill->invoke($class, $options);

            expect($options['basePath'])->equals('resources/');

            expect($options['collection'])->equals('tests');

            expect($options['model'])->equals('Test');

            expect($options['controller'])->equals('TestController');

            expect($options['isElement'])->true();

            expect($options)->hasntKey('isCollection');

            expect($options['allowPutAll'])->false();

            expect($options['allowDeleteAll'])->false();
        });

        $this->specify('provides default value from collection.', function () use ($class, $fill) {
            $options = [
                'collection' => 'things'
            ];

            $options = $fill->invoke($class, $options);

            expect($options['basePath'])->equals('resources/');

            expect($options['collection'])->equals('things');

            expect($options['model'])->equals('Thing');

            expect($options['controller'])->equals('ThingController');

            expect($options['isElement'])->true();

            expect($options)->hasntKey('isCollection');

            expect($options['allowPutAll'])->false();

            expect($options['allowDeleteAll'])->false();
        });

        $this->specify('keeps given values', function () use ($class, $fill) {
            $options = [
                'basePath' => 'here',
                'collection' => 'things',
                'element' => 'item',
                'model' => 'Anything',
                'controller' => 'SiteController',
                'isCollection' => true,
                'allowPutAll' => true,
                'allowDeleteAll' => true
            ];

            $options = $fill->invoke($class, $options);

            expect($options['basePath'])->equals('here/');

            expect($options['collection'])->equals('things');

            expect($options['element'])->equals('item');

            expect($options['model'])->equals('Anything');

            expect($options['controller'])->equals('SiteController');

            expect($options['isCollection'])->true();

            expect($options)->hasntKey('isElement');

            expect($options['allowPutAll'])->true();

            expect($options['allowDeleteAll'])->true();
        });
    }

    /**
     * Specs for RouteGenerator::execute()
     */
    public function testExecuteMethod()
    {
        $class = $this->prepareSpecify();

        $this->specify('renders if one class name is passed.', function () use ($class) {
            View::shouldReceive('make->render')
                ->once()
                ->andReturn('test output');

            $params = ['element' => 'test'];
            expect($class->execute($params))->equals('test output');
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
        $class = new ReflectionClass('Impleri\Resource\RouteGenerator');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * Set up for each spec
     */
    protected function prepareSpecify()
    {
        $this->cleanSpecify();
        $this->afterSpecify(function () {
            Mockery::close();
        });

        return new Impleri\Resource\RouteGenerator;
    }
}

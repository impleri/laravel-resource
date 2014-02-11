<?php namespace Impleri\Resource;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

/**
 * Router Helper
 *
 * Small class to extend Router functions
 */
class Router
{
    /**
     * Group Resources
     *
     * Construct routes to resources.
     * @param  array  $resources Array of resources indexed by resource name.
     * @param  string $prefix    Route prefix to prepend to resource routes.
     */
    public static function resources($resources, $options = array())
    {
        if (!isset($options['prefix'])) {
            $options['prefix'] = 'resources';
        }
        if (!isset($options['pattern'])) {
            $options['pattern'] = '%sController';
        }

        Route::group(
            $options,
            function () use ($resources, $options) {
                foreach ($resources as $name => $data) {
                    if (!is_array($data)) {
                        $data = array(
                            'controller' => $data
                        );
                    }

                    if (!isset($data['controller'])) {
                        $data['controller'] = sprintf($options['pattern'], Str::studly($name));
                    }

                    if (is_numeric($name)) {
                        $name = $data['controller'];
                    }

                    $controller = $data['controller'];

                    // Assume an element if neither is specified
                    if (!isset($data['isCollection']) && !isset($data['isElement'])) {
                        $data['isElement'] = true;
                    }

                    if (isset($data['isCollection']) && $data['isCollection']) {
                        self::collection($name, $controller, $data);
                    }

                    if (isset($data['isElement']) && $data['isElement']) {
                        self::element($name, $controller, $data);
                    }
                }
            }
        );
    }

    /**
     * Route Collection
     *
     * Construct the routes for a resource collection.
     * @param  string $resource Resource name
     * @param  array  $data     Resource route options
     */
    public static function collection($resource, $controller, array $options = array())
    {
        $pluralize = (isset($options['pluralize'])) ? $options['pluralize'] : true;
        $putCollection = (isset($options['allowPutAll'])) ? $options['allowPutAll'] : false;
        $deleteCollection = (isset($options['allowDeleteAll'])) ? $options['allowDeleteAll'] : false;

        if ($pluralize) {
            $resource = Str::plural($resource);
        }

        $collection_fmt = $controller . '@%sCollection';
        Route::get($resource, sprintf($collection_fmt, 'get'));           // list

        if ($putCollection) {
            Route::put($resource, sprintf($collection_fmt, 'put'));       // replace
        }

        if ($deleteCollection) {
            Route::delete($resource, sprintf($collection_fmt, 'delete')); // delete
        }
    }

    /**
     * Route Element
     *
     * Construct the routes for a resource element.
     * @param  string $resource Resource name
     * @param  array  $data     Resource route options
     */
    public static function element($resource, $controller, array $options = array())
    {
        $pluralize = (isset($options['pluralize'])) ? $options['pluralize'] : true;

        if ($pluralize) {
            $resource = Str::plural($resource);
        }

        $collection_fmt = $controller . '@%sCollection';

        $element = sprintf('%1$s/{%1$s}', $resource);
        $element_fmt = $controller . '@%sElement';
        Route::get($element, sprintf($element_fmt, 'get'));       // read
        Route::post($resource, sprintf($collection_fmt, 'post')); // create
        Route::post($element, sprintf($element_fmt, 'post'));     // [deprecated]
        Route::put($element, sprintf($element_fmt, 'put'));       // update
        Route::delete($element, sprintf($element_fmt, 'delete')); // delete
    }
}

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
     * Group Routes
     *
     * Construct routes to resources.
     * @param  array  $resources Array of resources indexed by resource name.
     * @param  string $prefix    Route prefix to prepend to resource routes.
     */
    public static function resources($resources, $prefix = 'resources')
    {
        Route::group(
            array('prefix' => $prefix),
            function () use ($resources) {
                foreach ($resources as $name => $data) {
                    if (!is_array($data)) {
                        $data = array(
                            'class' => $data
                        );
                    }

                    if (is_numeric($name)) {
                        $name = $data['class'];
                    }

                    $controller = $data['class'];

                    self::resource($name, $controller, $data);
                }
            }
        );
    }

    /**
     * Route Resource
     *
     * Construct the routes for a resource.
     * @param  string $resource Resource name
     * @param  array  $data     Resource route options
     */
    public static function resource($resource, $controller, array $options = array())
    {
        $pluralize = (isset($options['pluralize'])) ? $options['pluralize'] : true;
        $hasItems = (isset($options['accessItems'])) ? $options['accessItems'] : true;
        $putCollection = (isset($options['allowSaveAll'])) ? $options['allowSaveAll'] : false;
        $deleteCollection = (isset($options['allowDeleteAll'])) ? $options['allowDeleteAll'] : false;

        if ($pluralize) {
            $resource = Str::plural($resource);
        }

        $collection_fmt = $controller . '@%sCollection';
        Route::get($resource, sprintf($collection_fmt, 'get'));

        if ($putCollection) {
            Route::put($resource, sprintf($collection_fmt, 'put'));
        }

        if ($deleteCollection) {
            Route::delete($resource, sprintf($collection_fmt, 'delete'));
        }

        if ($hasItems) {
            $element = sprintf('%1$s/{%1$s}', $resource);
            $element_fmt = $controller . '@%sElement';
            Route::get($element, sprintf($element_fmt, 'get'));
            Route::post($resource, sprintf($collection_fmt, 'post'));
            Route::post($element, sprintf($element_fmt, 'post'));
            Route::put($element, sprintf($element_fmt, 'put'));
            Route::delete($element, sprintf($element_fmt, 'delete'));
        }
    }
}

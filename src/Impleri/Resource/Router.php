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
    public static function group($resources, $prefix = 'resources')
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

                    self::resource($name, $data);
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
    public static function resource($resource, $data = array())
    {
        $controller = (isset($data['class'])) ? $data['class'] : $resource;
        $hasItems = (isset($data['hasItems'])) ? $data['hasItems'] : true;
        $putCollction = (isset($data['allowSaveAll'])) ? $data['allowSaveAll'] : true;
        $deleteCollction = (isset($data['allowDeleteAll'])) ? $data['allowDeleteAll'] : false;
        $pluralize = (isset($data['pluralize'])) ? $data['pluralize'] : true;
        $addToCollection = (isset($data['collectionForm'])) ? $data['collectionForm'] : 'create';
        $editElement = (isset($data['elementForm'])) ? $data['elementForm'] : 'edit';

        if ($pluralize) {
            $resource = Str::plural($resource);
        }

        $collection_fmt = $controller . '@%sCollection';
        Route::get($resource, sprintf($collection_fmt, 'get'));
        Route::post($resource, sprintf($collection_fmt, 'post'));

        if ($putCollction) {
            Route::put($resource, sprintf($collection_fmt, 'put'));
        }

        if ($deleteCollction) {
            Route::delete($resource, sprintf($collection_fmt, 'delete'));
        }

        if ($addToCollection) {
            Route::get($resource . '/' . $addToCollection, sprintf($collection_fmt, 'addTo'));
        }

        if ($hasItems) {
            $element = sprintf('%1$s/{%1$s}', $resource);
            $element_fmt = $controller . '@%sElement';
            Route::get($element, sprintf($element_fmt, 'get'));
            Route::post($element, sprintf($element_fmt, 'post'));
            Route::put($element, sprintf($element_fmt, 'put'));
            Route::delete($element, sprintf($element_fmt, 'delete'));

            if ($editElement) {
                Route::get($element . '/' . $editElement, sprintf($element_fmt, 'edit'));
            }
        }
    }
}

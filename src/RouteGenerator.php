<?php namespace Impleri\Resource;

use BadFunctionCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

/**
 * Controller Generator
 *
 * Small class to generate resource routes.
 */
class RouteGenerator extends Generator
{
    /**
     * Default view to use for generating routes.
     * @var string
     */
    protected $view = 'resource::routes';

    /**
     * Fill Options
     *
     * Boilerplate method to inflate generator options using defaults.
     * @param  array  $options Variables to pass to the view
     * @throws BadFunctionCallException If missing element/collection from $options
     */
    protected function fillOptions(&$options)
    {
        // Need at least a name to use
        if (!isset($options['collection']) && !isset($options['element'])) {
            throw new BadFunctionCallException('A collection or element name is required.');
        }

        // Set the default base path if none is
        if (!isset($options['basePath'])) {
            $options['basePath'] = $this->path;
        }

        if (!isset($options['collection'])) {
            // Pluralize the element
            $options['collection'] = Str::plural($options['element']);
        } elseif (!isset($options['element'])) {
            // Singularize the collection
            $options['element'] = Str::singular($options['collection']);
        }

        // Set the model from the element
        if (!isset($options['model'])) {
            $options['model'] = Str::studly($options['element']);
        }

        // Set the controller from the model
        if (!isset($options['controller'])) {
            $options['controller'] = $options['model'] . 'Controller';
        }

        // Assume an element if neither is specified
        if (!isset($options['isCollection']) && !isset($options['isElement'])) {
            $options['isElement'] = true;
        }

        // Assume PUT collection is false
        if (!isset($options['allowPutAll'])) {
            $options['allowPutAll'] = false;
        }

        // Assume DELETE collection is false
        if (!isset($options['allowDeleteAll'])) {
            $options['allowDeleteAll'] = false;
        }
    }

    /**
     * Execute
     *
     * Generic method to generate resource class files.
     * @param  array  $options Variables to pass to the view
     * @return string          Routes to add to routes.php
     */
    public function execute($options)
    {
        $this->fillOptions($options);

        // Pass options to the view and return the rendered string
        return View::make($this->view, $options)->render();
    }
}

<?php namespace Impleri\Resource;

use Illuminate\Support\Str;

/**
 * Generator Helper
 *
 * Small class to generate resource scaffolding
 */
class Generator
{
    /**
     * Default base path.
     * @var string
     */
    private static $base_path = 'resources';

    /**
     * Generate
     *
     * Builds routes for insertion by toolbox:routes.
     * @param  array $options Variables to pass to the view
     * @return string         The view parsed and rendered into a string
     */
    public static function routes($options)
    {
        // Need at least a name to use
        if (!isset($options['collection']) && !isset($options['element'])) {
            throw new \InvalidArgumentException('A collection or element name is required.');
        }

        // Set the default base path if none is
        if (!isset($options['basePath'])) {
            $options['basePath'] = self::$base_path;
        }

        // Pluralize the resource
        if (!isset($options['collection']) && isset($options['element'])) {
            $options['collection'] = Str::plural($options['element']);
        }

        // Singularize the element
        if (!isset($options['element']) && isset($options['collection'])) {
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

        // Pass options to the view and return the rendered string
        return app()['view']->make('resource::routes', $options)->render();
    }
}

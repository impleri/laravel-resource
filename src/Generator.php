<?php namespace Impleri\Resource;

use BadFunctionCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

/**
 * Generator Helper
 *
 * Small class to generate resource scaffolding
 */
class Generator
{
    /**
     * Default namespace.
     * @var string
     */
    private static $base_namespace = '';

    /**
     * Default routes path for resources.
     * @var string
     */
    private static $base_routes_path = 'resources';

    /**
     * Default save path for controllers.
     * @var string
     */
    private static $base_controller_path = 'app/controllers/resources/';

    /**
     * Default view to use for generating controllers.
     * @var string
     */
    private static $default_controller_view = 'resource::controller';


    /**
     * Routes
     *
     * Builds routes for insertion by toolbox:routes.
     * @param  array $options Variables to pass to the view
     * @return string         The view parsed and rendered into a string
     * @throws BadFunctionCallException If missing element/collection from $options
     */
    public static function routes($options)
    {
        // Need at least a name to use
        if (!isset($options['collection']) && !isset($options['element'])) {
            throw new BadFunctionCallException('A collection or element name is required.');
        }

        // Set the default base path if none is
        if (!isset($options['basePath'])) {
            $options['basePath'] = self::$base_routes_path;
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

    /**
     * Controllers
     *
     * Stub app-level controllers by toolbox:controllers.
     * @param  array $options Variables to pass to the view
     * @return int            Number of controllers written
     * @throws BadFunctionCallException If missing element/collection from $options
     */
    public static function controllers($options)
    {
        // Set the default base path if none is
        if (!isset($options['basePath'])) {
            $options['basePath'] = self::$base_controller_path;
        }

        return self::files($options, self::$default_controller_view);
    }

    /**
     * Files
     *
     * Generic method to generate resource class files produced through toolbox commands.
     * @param  array $options Variables to pass to the view
     * @return int            Number of controllers written
     * @throws BadFunctionCallException If missing element/collection from $options
     */
    private static function files($options, $view)
    {
        $count = 0;

        // Need at least a name to use
        if (!isset($options['classes']) || empty($options['classes'])) {
            throw new BadFunctionCallException('Classes are required for generation.');
        }

        // Set the default base path if none is
        if (!isset($options['baseNamespace'])) {
            $options['baseNamespace'] = self::$base_namespace;
        }

        $baseNamespace = $options['baseNamespace'];
        $basePath = $options['basePath'];
        $basePath .= (empty($baseNamespace)) ? '' : str_replace('\\', '/', $baseNamespace);

        foreach ($options['classes'] as $name => $class) {
            // Reset namespace and save path
            $namespace = $baseNamespace;
            $saveTo = $basePath;

            // Add to the save path and namespace
            if (isset($class['subNamespace'])) {
                $namespace .= $class['subNamespace'];
                $saveTo .= str_replace('\\', '/', $class['subNamespace']);
            }

            // Override the class name with the array value
            if (isset($class['name'])) {
                $name = $class['name'];
            }

            if (!isset($name) || is_numeric($name)) {
                throw new BadFunctionCallException('Every class must have a name.');
            }

            $saveTo .= $name;

            // Override the view if one is passed
            if (isset($class['view'])) {
                $view = $class['view'];
            }

            // Set some necessary variables for the view
            $data = [
                'namespace' => $namespace,
                'class' => $name,
            ];

            // Overload data provided to the view
            if (isset($class['data'])) {
                $data = array_merge($data, $class['data']);
            }

            // Generate the contents
            $contents = app()['view']->make($view, $data)->render();

            // Remove the old backup
            if (File::exists($saveTo . '.bak.php')) {
                File::delete($saveTo . '.bak.php');
            }

            // Back up the existing file
            if (File::exists($saveTo . '.php')) {
                File::move($saveTo . '.php', $saveTo . '.bak.php');
            }

            // Save the new file
            if (File::put($saveTo . '.php', $contents)) {
                $count++;
            }
        }

        // Return count to the toolbox
        return $count;
    }
}

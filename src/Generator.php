<?php namespace Impleri\Resource;

use BadFunctionCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Impleri\Resource\Contracts\GeneratorInterface;

/**
 * Generator
 *
 * Ancestor class for resource generators with a lot of boilerplate code.
 */
abstract class Generator implements GeneratorInterface
{
    /**
     * Default namespace.
     * @var string
     */
    protected $namespace = '';

    /**
     * Default file path for resources.
     * @var string
     */
    protected $path = 'resources';

    /**
     * Default view to use for generating a resource.
     * @var string
     */
    protected $view = 'resource::view';

    /**
     * Execute
     *
     * Generic method to generate resource class files.
     * @param  array  $options Variables to pass to the view
     * @return int             Number of controllers written
     */
    public function execute($options)
    {
        $count = 0;
        $options = $this->fillOptions($options);

        foreach ($options['classes'] as $name => $class) {
            $data = $this->fillClass($name, $class, $options);

            // Override the view if one is passed
            $view = (isset($class['view'])) ? $class['view'] : $this->view;

            // Generate the contents
            $contents = View::make($view, $data)->render();

            // Remove the old backup
            if ($this->save($data['file'], $contents)) {
                $count++;
            } else {
                // Some kind of warning here
            }
        }

        // Return count to the toolbox
        return $count;
    }

    /**
     * Fill Options
     *
     * Boilerplate method to inflate generator options using defaults.
     * @param  array  $options Variables to pass to the view
     * @throws BadFunctionCallException If missing element/collection from $options
     */
    protected function fillOptions($options)
    {
        // Need at least a name to use
        if (!isset($options['classes']) || empty($options['classes'])) {
            throw new BadFunctionCallException('Classes are required for generation.');
        }

        // Set the default base path if none is
        if (!isset($options['baseNamespace'])) {
            $options['baseNamespace'] = $this->namespace;
        }

        // Set the default base path if none is
        if (!isset($options['basePath'])) {
            $options['basePath'] = $this->path;
        }

        // Flesh out the base path
        $basePath = $this->trailingSeparator($options['basePath']);

        // Add base namespace to base path
        $baseNamespace = $options['baseNamespace'];
        $basePath .= (empty($baseNamespace)) ? '' : $this->convertNamespace($baseNamespace);
        $basePath = $this->trailingSeparator($basePath);

        $options['basePath'] = $basePath;

        return $options;
    }

    /**
     * Fill Class
     *
     * Boilerplate method to inflate options for a specific class using defaults.
     * @param  str|int $name    The class name
     * @param  array   $class   Class-specific options to pass to the view
     * @param  array   $options Global options to pass to the view
     * @throws BadFunctionCallException If missing name from $name and $options
     */
    protected function fillClass($name, $class, $options)
    {
        $namespace = $options['baseNamespace'];
        $saveTo = $this->trailingSeparator($options['basePath']);

        // Add to the save path and namespace
        if (isset($class['subNamespace'])) {
            if (!empty($namespace)) {
                $namespace = $this->trailingSeparator($namespace, '\\');
            }
            $namespace .= $class['subNamespace'];
            $saveTo .= $this->convertNamespace($class['subNamespace']);
        }

        $saveTo = $this->trailingSeparator($saveTo);

        // Override the class name with the array value
        if (isset($class['name'])) {
            $name = $class['name'];
        }

        if (!isset($name) || is_numeric($name) || empty($name)) {
            throw new BadFunctionCallException('Every class must have a name.');
        }

        $saveTo .= $name;

        // Set some necessary variables for the view
        $data = [
            'namespace' => $namespace,
            'class' => $name,
            'file' => $saveTo
        ];

        // Overload data provided to the view
        if (isset($class['data'])) {
            $data = array_merge($data, $class['data']);
        }

        return $data;
    }

    /**
     * Save
     *
     * Boilerplate method to save file and backup existing one.
     * @param  string $path     File path
     * @param  string $contents Contents to save to the file
     * @return bool             True on sucess, false otherwise
     */
    protected function save($path, $contents)
    {
        $success = false;

        // Remove the old backup
        if (File::exists($path . '.bak.php')) {
            File::delete($path . '.bak.php');
        }

        // Back up the existing file
        if (File::exists($path . '.php')) {
            File::move($path . '.php', $path . '.bak.php');
        }

        // Save the new file
        if (File::put($path . '.php', $contents)) {
            $success = true;
        }

        return $success;
    }

    /**
     * Trailing Separator
     *
     * Ensures there is a trailing separator on a string
     * @param  string $string String to test
     * @param  string $end    Separator to append
     * @return string         Modified string
     */
    protected function trailingSeparator($string, $end = '/')
    {
        if (substr($string, -strlen($end)) !== $end) {
            $string .= $end;
        }

        return $string;
    }

    /**
     * Convert Namespace
     *
     * Convert backslash from namespace to forward slash for file path.
     * @param  string $string Namespace to convert.
     * @return string         Namespace converted to a path.
     */
    protected function convertNamespace($string)
    {
        return str_replace('\\', '/', $string);
    }
}

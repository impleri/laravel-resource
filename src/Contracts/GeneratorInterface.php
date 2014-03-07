<?php namespace Impleri\Resource\Contracts;

/**
 * Generator Interface
 *
 * Small class to generate resource scaffolding
 */
interface GeneratorInterface
{
    /**
     * Execute
     *
     * Runs the generation process.
     * @param  array $options Variables to pass to the view
     */
    public function execute($options);
}

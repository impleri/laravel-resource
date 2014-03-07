<?php namespace Impleri\Resource;

/**
 * Controller Generator
 *
 * Small class to generate resource controllers.
 */
class ControllerGenerator extends Generator
{
    /**
     * Default save path for controllers.
     * @var string
     */
    protected $path = 'app/controllers/resources/';

    /**
     * Default view to use for generating controllers.
     * @var string
     */
    protected $view = 'resource::controller';
}

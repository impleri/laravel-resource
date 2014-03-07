<?php namespace Impleri\Resource;

use Illuminate\Support\ServiceProvider;

class ResourceServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->package('impleri/resource', null, realpath(__DIR__));
    }

    public function register()
    {
        App::bind(
            'resource.controller',
            function () {
            return new ControllerGenerator;
            }
        );

        App::bind(
            'resource.route',
            function () {
            return new RouteGenerator;
            }
        );
    }
}

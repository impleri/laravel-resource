<?php namespace Impleri\Resource\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Controller Generator Facade
 */
class RouteGenerator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'resource.route';
    }
}

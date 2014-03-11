<?php namespace Impleri\Resource\Controllers;

use Illuminate\Support\Str;
use Impleri\Resource\ResourceTrait;

/**
 * Base REST Controller
 *
 * Provides common functionality for REST resource controllers.
 */
class Base
{
    /**
     * Consume the Resource trait
     */
    use ResourceTrait;

    /**
     * Name of the element to use.
     * @var string
     */
    protected $elementName = 'item';

    /**
     * Name of the collection to use.
     * @var string
     */
    protected $collectionName = 'items';

    /**
     * Name of the related model to use.
     * @var string
     */
    protected $model = 'Model';

    /**
     * Output data collector.
     * @var array
     */
    protected $data = [];

    /**
     * Constructor
     */
    public function __construct($elementName = 'item', $collectionName = '')
    {
        // Set the element name (singular) first
        $this->elementName = $elementName;

        // Pluralise the element name if a collection name is not given
        if (empty($collectionName)) {
            $collectionName = Str::plural($this->elementName);
        }

        // Set the collection name (plural)
        $this->collectionName = $collectionName;

        $this->data['success'] = false;
        $this->data['json'] = [];
        $this->data['errors'] = [];
    }

    /**
     * Set Response
     *
     * Common method to set response data.
     * @param string  $key    Key name
     * @param mixed   $value  Value
     * @param boolean $toJson Also save for JSON
     */
    protected function setResponse($key, $value, $toJson = true)
    {
        $this->data[$key] = $value;
        if ($toJson) {
            $this->data['json'][$key] = (method_exists($value, 'toArray'))
                ? $value->toArray()
                : $value;
        }
    }

    /**
     * Make Response
     *
     * Common method to generate a response.
     * @param string  $action Response action (for view name)
     * @return \Illuminate\Http\Response The response object
     */
    protected function makeResponse($action)
    {
        $view = $this->elementName . '.' . $action;
        return $this->respond($this->data, $view);
    }
}

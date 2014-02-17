<?php namespace Impleri\Resource\Controllers;

use Impleri\Resource\Contracts\CollectionInterface;

/**
 * Element Resource
 *
 * This controller handles CRUD actions for generic element resources.
 */
class CollectionElement extends Element implements CollectionInterface
{
    /**
     * Paginate
     *
     * Number of items to include in pagination (0 = no pagination).
     * @var integer
     */
    protected $paginate = 0;

    /**
     * Constructor
     */
    public function __construct($elementName = 'item', $collectionName = '')
    {
        parent::__construct($elementName, $collectionName);

        // Ensure the collection variable always exists for views
        $this->data[$this->collectionName] = [];
    }

    /**
     * Get Collection
     *
     * Processes input to return a paginated collection of matched items.
     * Corresponds to the RESTful GET action for the collection.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function getCollection()
    {
        $model = $this->model;
        $items = ($this->paginate > 0) ? $model::paginate($this->paginate) : $model::query()->get();

        if (!$items->isEmpty()) {
            $this->setResponse($this->collectionName, $items);
        }

        return $this->makeResponse('browse');
    }

    /**
     * Put Collection
     *
     * Processes input to overwrite the entire collection. Corresponds to the
     * RESTful PUT action for the collection.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function putCollection()
    {
        // Disable by default
        return $this->notSupported();
    }

    /**
     * Delete Collection
     *
     * Processes input to delete the entire collection. Corresponds to the
     * RESTful DELETE action for the collection.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function deleteCollection()
    {
        // Disable by default
        return $this->notSupported();
    }
}

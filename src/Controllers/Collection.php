<?php namespace Impleri\Resource\Controllers;

use Impleri\Resource\Contracts\CollectionInterface;

/**
 * Collection Resource
 *
 * This controller handles CRUD actions for generic element resources.
 */
class Collection extends Base implements CollectionInterface
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
     * Post Collection
     *
     * Processes input to create an individual item within the collection.
     * Corresponds to the RESTful POST action for the collection.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function postCollection()
    {
        $item = new $this->model;

        // We expect Ardent to handle validation
        $this->data['success'] = $item->save();

        if ($this->data['success']) {
            $this->setResponse($this->elementName, $item);
        }

        return $this->makeResponse('add');
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

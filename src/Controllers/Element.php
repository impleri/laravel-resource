<?php namespace Impleri\Resource\Controllers;

use Illuminate\Database\Eloquent\Model;
use Impleri\Resource\Contracts\ElementInterface;

/**
 * Element API Controller
 *
 * Generic controller for handling CRUD actions for individual elements
 */
class Element extends Base implements ElementInterface
{
    /**
     * Constructor
     */
    public function __construct($elementName = 'item', $collectionName = '')
    {
        parent::__construct($elementName, $collectionName);

        // Ensure the collection variable always exists for views
        $this->data[$elementName] = false;
    }

    /**
     * Get Element
     *
     * Processes input to retrieve an individual item within the collection.
     * Corresponds to the RESTful GET action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function getElement(Model $item)
    {
        $this->setResponse($this->elementName, $item);

        return $this->makeResponse('read');
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
     * Put Element
     *
     * Processes input to replace or create an individual item within the collection.
     * Corresponds to the RESTful PUT action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function putElement(Model $item = null)
    {
        return (is_null($item)) ? $this->postCollection() : $this->patchElement($item);
    }

    /**
     * Patch Element
     *
     * Processes input to update an individual item within the collection.
     * Corresponds to the RESTful PATCH action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function patchElement(Model $item)
    {
        $this->data['success'] = $item->save();

        return $this->makeResponse('edit');
    }

    /**
     * Delete Item
     *
     * Processes input to remove an individual item from the collection.
     * Corresponds to the RESTful DELETE action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function deleteElement(Model $item)
    {
        $this->data['success'] = $item->delete();

        return $this->makeResponse('delete');
    }
}

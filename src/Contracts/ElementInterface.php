<?php namespace Impleri\Resource\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Element Resource Interface
 *
 * Define required methods for handling elements RESTfully.
 */
interface ElementInterface
{
    /**
     * Get Element
     *
     * Processes input to retrieve an individual item within the collection.
     * Corresponds to the RESTful GET action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function getElement(Model $model);

    /**
     * Post Collection
     *
     * Processes input to create an individual item within the collection.
     * Corresponds to the RESTful POST action for the collection.
     * This definition is mirrored in CollectionInterface.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function postCollection();

    /**
     * Put Element
     *
     * Processes input to replace or create an individual item within the collection.
     * Corresponds to the RESTful PUT action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function putElement(Model $model = null);

    /**
     * Patch Element
     *
     * Processes input to update an individual item within the collection.
     * Corresponds to the RESTful PATCH action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function patchElement(Model $model);

    /**
     * Delete Item
     *
     * Processes input to remove an individual item from the collection.
     * Corresponds to the RESTful DELETE action for the element/item.
     * @param  \Illuminate\Database\Eloquent\Model $model Eloquent Model to autoload
     * @return \Illuminate\Http\Response           Laravel response
     */
    public function deleteElement(Model $model);
}

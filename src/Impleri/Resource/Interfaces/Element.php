<?php namespace Impleri\Resource\Interfaces;

/**
 * Element Resource Interface
 *
 * Define required methods for handling elements RESTfully.
 */
interface Element
{
    /**
     * Get Element
     *
     * Processes input to retrieve an individual item within the collection.
     * Corresponds to the RESTful GET action for the element/item.
     * @param int $rid Resource ID
     * @return \Illuminate\Http\Response Laravel response
     */
    public function getElement($rid = 0);

    /**
     * Post Collection
     *
     * Processes input to create an individual item within the collection.
     * Corresponds to the RESTful POST action for the collection.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function postCollection();

    /**
     * Post Item
     *
     * Processes input from older browsers to properly route a RESTful request.
     * @param int $rid Resource ID
     * @return \Illuminate\Http\Response Laravel response
     */
    public function postElement($rid = 0);

    /**
     * Put Item
     *
     * Processes input to update an individual item within the collection.
     * Corresponds to the RESTful PUT action for the element/item.
     * @param int $rid Resource ID
     * @return \Illuminate\Http\Response Laravel response
     */
    public function putElement($rid = 0);

    /**
     * Delete Item
     *
     * Processes input to remove an individual item from the collection.
     * Corresponds to the RESTful DELETE action for the element/item.
     * @param int $rid Resource ID
     * @return \Illuminate\Http\Response Laravel response
     */
    public function deleteElement($rid = 0);
}

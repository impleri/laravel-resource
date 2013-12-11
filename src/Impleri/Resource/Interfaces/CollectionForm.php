<?php namespace Impleri\Resource\Interfaces;

/**
 * Collection Resource Forms Interface
 *
 * Define additional methods for handling collection data.
 */
interface CollectionForm extends Collection
{
    /**
     * Add To Collection
     *
     * Shows form to add a new element to the collection.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function addToCollection();
}

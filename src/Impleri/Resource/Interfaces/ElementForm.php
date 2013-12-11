<?php namespace Impleri\Resource\Interfaces;

/**
 * Element Resource Forms Interface
 *
 * Define additional methods for handling element data.
 */
interface ElementForm extends Element
{
    /**
     * Edit Element
     *
     * Shows form to edit an existing element.
     * @return \Illuminate\Http\Response Laravel response
     */
    public function editElement($rid = 0);
}

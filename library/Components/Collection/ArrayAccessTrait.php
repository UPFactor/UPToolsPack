<?php

namespace UPTools\Components\Collection;

/**
 * Methods to provide accessing objects as arrays.
 *
 * @package UPTools\Components\Collection
 */
trait ArrayAccessTrait {

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key){
        return array_key_exists($key, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key){
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value){
        if (is_null($key)) {
            $this->items[] = $this->dataItemController($value, $key);
        } else {
            $this->items[$key] = $this->dataItemController($value, $key);
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  mixed  $key
     * @return void
     */
    public function offsetUnset($key){
        unset($this->items[$key]);
    }
}
<?php

namespace UPTools;

/**
 * Trait CollectionSetTrait
 *
 * @see CollectionBaseTrait
 * @package UPTools
 */
trait CollectionSetTrait {

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param mixed|null $value
     * @return $this
     */
    public function prepend($value = null){
        Arr::prepend($this->items, $this->dataItemController($value));
        return $this;
    }

    /**
     * Push an item onto the end of the collection.
     *
     * @param mixed|null $value
     * @return $this
     */
    public function push($value = null){
        array_push($this->items, $this->dataItemController($value));
        return $this;
    }

    /**
     * Pad collection to the specified length with a value.
     *
     * @param int $size
     * @param mixed|null $value
     * @return $this
     */
    public function pad($size, $value = null){
        $this->items = array_pad($this->items, $size, $this->dataItemController($value));
        return $this;
    }

    /**
     * Set all empty elements to the specified default value.
     *
     * @param mixed|null $default
     * @return $this
     */
    public function defaults($default = null){
        Arr::defaults($this->items, $this->dataItemController($default));
        return $this;
    }

    /**
     * Removes false values from the collection.
     *
     * @return $this
     */
    public function compact(){
        $this->items = Arr::compact($this->items);
        return $this;
    }

    /**
     * Join array elements with a string.
     *
     * @param string|null $glue
     * @return string
     */
    public function concat($glue = null){
        return implode($glue, $this->items);
    }
}
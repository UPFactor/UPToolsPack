<?php

namespace UPTools;

/**
 * Trait CollectionConversionTrait
 *
 * @see CollectionBaseTrait
 * @package UPTools
 */
trait CollectionConversionTrait {

    /**
     * Returns the JSON representation of the collection.
     *
     * @param int|int[]|string|string[]|null $keys
     * @return string
     */
    public function toJson($keys = null){
        return Arr::toJson($this->items, $keys);
    }

    /**
     * Returns the serialized representation of the collection.
     *
     * @param int|int[]|string|string[]|null $keys
     * @return string
     */
    public function toSerialize($keys = null){
        return Arr::toSerialize($this->items, $keys);
    }

    /**
     * Convert the collection to an HTTP request string.
     *
     * @param int|int[]|string|string[]|null $keys
     * @return string
     */
    public function toQuery($keys = null){
        return Arr::toQuery($this->items, $keys);
    }

    /**
     * Flatten a multi-dimensional collection with dots.
     *
     * @return array
     */
    public function toDot(){
        return Arr::toDot($this->items);
    }

}
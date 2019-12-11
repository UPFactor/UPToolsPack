<?php

namespace UPTools\Components\Collection;

use UPTools\Arr;

/**
 * Methods for combining collections.
 *
 * @see BaseTrait
 * @package UPTools\Components\Collection
 */
trait FusionTrait {

    /**
     * Removes duplicate values from a collection.
     *
     * @return static
     */
    public function unique(){
        $this->items = Arr::unique($this->items);
        return $this;
    }

    /**
     * Retrieve a new collection with unique elements of all transferred arrays or collections.
     * The order of the elements will be determined by the order of their appearance in the source arrays.
     *
     * @param mixed ...$values
     * @return static
     */
    public function union(...$values){
        foreach ($values as $key => $value){
            $values[$key] = $this->dataFusionController($value);
        }

        $this->items = Arr::union($this->items, ...$values);

        return $this;
    }

    /**
     * Merge the collection with the given items.
     *
     * @param mixed ...$values
     * @return static
     */
    public function merge(...$values){
        foreach ($values as $key => $value){
            $values[$key] = $this->dataFusionController($value);
        }

        $this->items = array_merge($this->items, ...$values);

        return $this;
    }

    /**
     * Returns a new collection containing all the values of the current collection that are missing
     * in all transferred arrays or collections.
     *
     * @param mixed ...$values
     * @return static
     */
    public function diff(...$values){
        foreach ($values as $key => $value){
            $values[$key] = $this->dataFusionController($value);
        }

        return $this->redeclare(Arr::diff($this->items, ...$values));
    }

    /**
     * Returns a new collection containing all the values of the current collection that are present
     * in all transferred arrays or collections.
     *
     * @param mixed ...$values
     * @return static
     */
    public function intersect(...$values){
        foreach ($values as $key => $value){
            $values[$key] = $this->dataFusionController($value);
        }

        return $this->redeclare(Arr::intersect($this->items, ...$values));
    }
}
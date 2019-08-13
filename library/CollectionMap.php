<?php

namespace UPTools;

/**
 * Class CollectionMap
 *
 * @package UPTools
 */
class CollectionMap {

    use CollectionBaseTrait,
        CollectionMapTrait;

    /**
     * Create a new collection.
     *
     * @param mixed $items
     */
    public function __construct($items = []){
        $this->reset($items);
    }
}
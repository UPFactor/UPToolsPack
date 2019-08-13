<?php

namespace UPTools;

/**
 * Class Collection
 *
 * @package UPTools
 */
class Collection {

    use CollectionBaseTrait,
        CollectionFusionTrait,
        CollectionSortingTrait,
        CollectionMultipleTrait,
        CollectionSetTrait,
        CollectionConversionTrait,
        CollectionMathsTrait;

    /**
     * Create a new collection.
     *
     * @param mixed $items
     */
    public function __construct($items = []){
        $this->reset($items);
    }
}
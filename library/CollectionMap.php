<?php

namespace UPTools;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use UPTools\Components\Collection\ArrayAccessTrait;
use UPTools\Components\Collection\BaseTrait;
use UPTools\Components\Collection\MapTrait;

/**
 * Class CollectionMap
 *
 * @package UPTools
 */
class CollectionMap implements Countable, IteratorAggregate, ArrayAccess {

    use BaseTrait,
        MapTrait,
        ArrayAccessTrait;

    /**
     * Create a new collection.
     *
     * @param mixed $items
     */
    public function __construct($items = []){
        $this->reset($items);
    }
}
<?php

namespace UPTools;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use UPTools\Components\Collection\ArrayAccessTrait;
use UPTools\Components\Collection\BaseTrait;
use UPTools\Components\Collection\ConversionTrait;
use UPTools\Components\Collection\FusionTrait;
use UPTools\Components\Collection\MathsTrait;
use UPTools\Components\Collection\MultipleTrait;
use UPTools\Components\Collection\SetTrait;
use UPTools\Components\Collection\SortingTrait;

/**
 * Class Collection
 *
 * @package UPTools
 */
class Collection implements Countable, IteratorAggregate, ArrayAccess {

    use BaseTrait,
        FusionTrait,
        SortingTrait,
        MultipleTrait,
        SetTrait,
        ConversionTrait,
        MathsTrait,
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
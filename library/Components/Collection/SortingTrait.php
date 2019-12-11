<?php

namespace UPTools\Components\Collection;

use Closure;

/**
 * Methods for sorting collections.
 *
 * @see BaseTrait
 * @package UPTools\Components\Collection
 */
trait SortingTrait {

    /**
     * Reverse items order.
     *
     * @return $this
     */
    public function reverse(){
        $this->items = array_reverse($this->items, true);
        return $this;
    }

    /**
     * Sort through each item with a callback.
     *
     * @param Closure|null $callback
     * @return $this
     */
    public function sort(Closure $callback = null){
        $callback ? uasort($this->items, $callback) : asort($this->items);
        return $this;
    }
}
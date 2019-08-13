<?php

namespace UPTools;

use Closure;

/**
 * Trait CollectionMultipleTrait
 *
 * @see CollectionBaseTrait
 * @package UPTools
 */
trait CollectionMultipleTrait {

    /**
     * Determines whether the collection contains an associative array.
     *
     * @param Closure|null $callable
     * @return bool
     */
    public function isAssoc(Closure $callable = null){
        if (Arr::isAssoc($this->items)){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }
        return false;
    }

    /**
     * Determines whether the collection contains a multidimensional array.
     *
     * @param Closure|null $callable
     * @return bool
     */
    public function isMultiple(Closure $callable = null){
        if (Arr::isMultiple($this->items)){
            if (!is_null($callable)){
                $callable($this);
            }
            return true;
        }
        return false;
    }

    /**
     * Get an item/items from the collection using "dot" notation.
     *
     * @param string|string[]|int|int[] $keys
     * @param mixed|null $default
     * @return mixed|array|null
     */
    public function get($keys, $default = null){
        return Arr::get($this->items, $keys, $default);
    }

    /**
     * Get and delete item/items from the collection by key/keys
     * using "dot" notation.
     *
     * @param string|string[]|int|int[] $keys
     * @param mixed|null $default
     * @return mixed|array|null
     */
    public function pull($keys, $default = null){
        return Arr::pull($this->items, $keys, $default);
    }

    /**
     * Check by key/keys if there is an item/items in the collection using "dot" notation.
     *
     * @param string|string[]|int|int[] $keys
     * @return bool
     */
    public function has($keys){
        return Arr::has($this->items, $keys);
    }

    /**
     * Set the collection item to a given value using "dot" notation.
     *
     * @param string|integer $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value){
        Arr::set($this->items, $key, $this->dataItemController($value, $key));
        return $this;
    }

    /**
     * Add an element to the collection using "dot" notation if it doesn't exist.
     *
     * @param string|integer $key
     * @param mixed $value
     * @return $this
     */
    public function add($key, $value){
        Arr::add($this->items, $key, $this->dataItemController($value, $key));
        return $this;
    }

    /**
     * Put an item in the collection by key.
     *
     * @param string|integer $key
     * @param mixed $value
     * @return $this
     */
    public function put($key, $value){
        $this->items[$key] = $this->dataItemController($value, $key);
        return $this;
    }

    /**
     * Remove item/items from the collection by key/keys
     * using "dot" notation.
     *
     * @param string|string[]|int|int[] $keys
     * @return $this
     */
    public function remove($keys){
        Arr::remove($this->items, $keys);
        return $this;
    }

    /**
     * Get a new collection based on the current one, excluding items
     * with the specified keys using "dot" notation.
     *
     * @param string|string[]|int|int[] $keys
     * @return static
     */
    public function except($keys){
        return $this->redeclare(Arr::except($this->items, $keys));
    }

    /**
     * Pluck new collection of values from the current collection by key/keys
     * using "dot" notation.
     *
     * @param $keys
     * @return static
     */
    public function pluck($keys){
        return static::wrap(Arr::pluck($this->items, $keys));
    }

    /**
     * Splits the collection into groups, united by the result of calling the "condition"
     * function for each element of the current collection.
     * To group by the value of the collection element, in the "condition" parameter,
     * specify the element key ("dot" notation) for which you want to group.
     *
     * @param callable|string|int $condition
     * @return static
     */
    public function group($condition){
        return static::wrap(Arr::group($this->items, $condition));
    }

    /**
     * Chunk the collection.
     * If the [$default] parameter is not passed, then the last array may contain less
     * values than specified in [$size], otherwise the value of the missing elements
     * will be filled with the value of [$default]
     *
     * @param int $size
     * @param mixed $default
     * @return static
     */
    public function chunk($size, $default = null){

        if (!is_numeric($size) or $size < 1){
            return $this->redeclare($this->items);
        }

        $chunks = array_chunk($this->items, $size, true);

        if (func_num_args() === 2){
            $chunks[] = array_pad(array_pop($chunks), $size, $default);
        }

        return static::wrap($chunks);
    }

    /**
     * Retrieve a new collection of items from the current collection that meet
     * the specified conditions using "dot" notation.
     *
     * @see \UPTools\Arr::where()
     *
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where($key, $operator = null, $value = null){
        return $this->redeclare(Arr::where($this->items, ...func_get_args()));
    }

    /**
     * Retrieve a new collection of items from the current collection that are equal
     * to one of the passed values using "dot" notation.
     *
     * @param string|int $key
     * @param array|string|int $values
     * @param bool $strict
     * @return static
     */
    public function whereIn($key, $values, $strict = false){
        return $this->redeclare(Arr::whereIn($this->items, $key, $values, $strict));
    }

    /**
     * Retrieve the new collection of items from the current collection that are not equal
     * to all specified values using the dot notation.
     *
     * @param string|int $key
     * @param array|string|int $values
     * @param bool $strict
     * @return static
     */
    public function whereNotIn($key, $values, $strict = false){
        return $this->redeclare(Arr::whereNotIn($this->items, $key, $values, $strict));
    }

    /**
     * Determine if all items in the collection pass the given test.
     *
     * @see \UPTools\Arr::every()
     *
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function every($key, $operator = null, $value = null){
        return Arr::every($this->items, ...func_get_args());
    }
}
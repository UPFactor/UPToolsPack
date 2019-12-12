<?php

namespace UPTools\Components\Collection;

/**
 * Methods for collections of key-value pairs.
 *
 * @see BaseTrait
 * @package UPTools\Components\Collection
 */
trait MapTrait {

    /**
     * Get item/items from collection by key/keys.
     *
     * @param string|string[]|int|int[] $keys
     * @param mixed|null $default
     * @return mixed|array|null
     */
    public function get($keys, $default = null){
        if (is_string($keys) or is_int($keys)){
            if (key_exists($keys, $this->items)){
                return $this->items[$keys];
            }
            return $default;
        }

        if (is_array($keys)){
            foreach ($keys as $index => $key){
                $keys[$index] = static::get($key, $default);
            }
            return $keys;
        }

        return $default;
    }

    /**
     * Get and delete item/items from collection by key/keys.
     *
     * @param string|string[]|int|int[] $keys
     * @param mixed|null $default
     * @return mixed|array|null
     */
    public function pull($keys, $default = null){
        if (is_string($keys) or is_int($keys)){
            if (key_exists($keys, $this->items)){
                $value = $this->items[$keys];
                unset($this->items[$keys]);
                return $value;
            }
            return $default;
        }

        if (is_array($keys)){
            $values = [];
            foreach ($keys as $index => $key){
                $keys[$index] = static::pull($key, $default);
            }
            return $values;
        }

        return null;
    }

    /**
     * Check by key/keys if there is an item/items in the collection.
     * Set the parameter [$strict], for additional checking for empty values.
     *
     * @param string|string[]|int|int[] $keys
     * @param bool $strict
     * @return bool
     */
    public function has($keys, $strict = false){
        if (is_string($keys) or is_int($keys)){
            if (key_exists($keys, $this->items)){
                if ($strict and empty($this->items[$keys])){
                    return false;
                }
                return true;
            }
            return false;
        }

        if (is_array($keys)){
            foreach ($keys as $key){
                if (array_key_exists($key, $this->items)){
                    if ($strict and empty($this->items[$key])){
                        return false;
                    }
                } else {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Set collection item for given value.
     *
     * @param string|int $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value){
        $this->items[$key] = $this->dataItemController($value, $key);
        return $this;
    }

    /**
     * Add an item to the collection if it does not exist.
     *
     * @param string|int $key
     * @param mixed $value
     * @return $this
     */
    public function add($key, $value){
        if (array_key_exists($key, $this->items)){
            return $this;
        }
        $this->items[$key] = $this->dataItemController($value, $key);
        return $this;
    }

    /**
     * Remove item/items from collection by key/keys.
     *
     * @param string|string[]|int|int[] $keys
     * @return $this
     */
    public function remove($keys){
        if (is_string($keys) or is_numeric($keys)){
            if (key_exists($keys, $this->items)){
                unset($this->items[$keys]);
            }
        }

        if (is_array($keys)){
            foreach ($keys as $index => $key){
                if (key_exists($key, $this->items)){
                    unset($this->items[$key]);
                }
            }
        }

        return $this;
    }
}
<?php

namespace UPTools;

use Closure;

/**
 * Class Arr
 *
 * @package UPTools
 */
class Arr {

    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     */
    static public function wrap($value){
        return is_array($value) ? $value : [$value];
    }

    /**
     * Determines if an array is associative.
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param  array  $array
     * @return bool
     */
    static public function isAssoc(array $array){
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    /**
     * Determines if an array is multidimensional.
     *
     * @param array $array
     * @return bool
     */
    static public function isMultiple(array $array){
        foreach ($array as $value) {
            if (is_array($value)) return true;
        }
        return false;
    }

    /**
     * Returns the JSON representation of an array.
     *
     * @param array $array
     * @param int|int[]|string|string[]|null $keys
     * @return string
     */
    static function toJson($array, $keys = null){
        if (!is_null($keys)){
            $array = static::pluck($array, $keys);
        }
        return json_encode($array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Returns the serialized representation of an array.
     *
     * @param array $array
     * @param int|int[]|string|string[]|null $keys
     * @return string
     */
    static public function toSerialize($array, $keys = null){
        if (!is_null($keys)){
            $array = static::pluck($array, $keys);
        }
        return serialize($array);
    }

    /**
     * Convert the array into a query string.
     *
     * @param $array
     * @param int|int[]|string|string[]|null $keys
     * @return string
     */
    static public function toQuery($array, $keys = null){
        if (!is_null($keys)){
            $array = static::pluck($array, $keys);
        }
        return http_build_query($array, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array $array
     * @param string $prepend
     * @return array
     */
    static public function toDot($array, $prepend = ''){
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                foreach (static::toDot($value, $prepend.$key.'.') as $subKey => $subValue){
                    $results[$subKey] = $subValue;
                }
            } else {
                $results[$prepend.$key] = $value;
            }
        }
        return $results;
    }

    /**
     * Retrieve array hash
     *
     * @param $array
     * @return string
     */
    static public function hash(array $array){
        return sha1(static::toJson($array));
    }

    /**
     * Get an item or items from an array using "dot" notation.
     *
     * @param array $array
     * @param string|string[]|int|int[] $keys
     * @param mixed|null $default
     * @return mixed|array|null
     */
    static public function get(array $array, $keys, $default = null){
        if (is_string($keys) or is_int($keys)){
            if (array_key_exists($keys, $array)){
                return $array[$keys];
            }

            if (strpos($keys, '.') === false) {
                return $default;
            }

            foreach (explode('.', $keys) as $segment) {
                if (is_array($array) and array_key_exists($segment, $array)) {
                    $array = $array[$segment];
                } else {
                    return $default;
                }
            }

            return $array;
        }

        if (is_array($keys)){
            foreach ($keys as $index => $key){
                $keys[$index] = static::get($array, $key, $default);
            }
            return $keys;
        }

        return $default;
    }

    /**
     * Get and delete item/items from the array by key/keys
     * using "dot" notation.
     *
     * @param array $array
     * @param string|string[]|int|int[] $keys
     * @param mixed|null $default
     * @return mixed|array|null
     */
    static public function pull(array &$array, $keys, $default = null){
        if (is_string($keys) or is_int($keys)){
            if (array_key_exists($keys, $array)){
                $value = $array[$keys];
                unset($array[$keys]);
                return $value;
            }

            if (strpos($keys, '.') === false) {
                return $default;
            }

            $keys = explode('.', $keys);

            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (key_exists($key, $array) and is_array($array[$key])){
                    $array = &$array[$key];
                } else {
                    return $default;
                }
            }

            $key = array_shift($keys);

            if (key_exists($key, $array)) {
                $value = $array[$key];
                unset($array[$key]);
                return $value;
            }

            return $default;
        }

        if (is_array($keys)){
            foreach ($keys as $index => $key){
                $keys[$index] = static::pull($array, $key, $default);
            }
            return $keys;
        }

        return $default;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array $array
     * @param string|array $keys
     * @return array
     */
    static public function only(array $array, $keys){
        $keys = static::wrap($keys);
        $result = [];

        foreach($keys as $key){
            if (!array_key_exists($key, $array)) continue;
            $result[$key] = $array[$key];
        }

        return $result;
    }

    /**
     * Get a new array based on the current one, eliminating
     * a subset of items.
     *
     * @param array $array
     * @param string|array $keys
     * @return array
     */
    static public function not(array $array, $keys){
        $keys = static::wrap($keys);

        foreach($keys as $key){
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Get a new array based on the current one, excluding items
     * with the specified keys using "dot" notation.
     *
     * @param array $array
     * @param string|string[]|int|int[] $keys
     * @return array
     */
    static public function except(array $array, $keys){
        if (!is_array($keys)){
            $keys = static::wrap($keys);
        }

        foreach ($keys as $key){
            static::remove($array, $key);
        }

        return $array;
    }

    /**
     * Check by key/keys if there is an item/items in the array using "dot" notation.
     *
     * @param array $array
     * @param string|string[]|int|int[] $keys
     * @return bool
     */
    static public function has(array $array, $keys){
        if (is_string($keys) or is_int($keys)){
            if (array_key_exists($keys, $array)){
                return true;
            }

            if (strpos($keys, '.') === false) {
                return false;
            }

            foreach (explode('.', $keys) as $segment) {
                if (is_array($array) and array_key_exists($segment, $array)) {
                    $array = $array[$segment];
                } else {
                    return false;
                }
            }

            return true;
        }

        if (is_array($keys) and !empty($keys)){
            foreach ($keys as $index => $key){
                if (static::has($array, $key) === false){
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Push an item onto the beginning of the array.
     *
     * @param array $array
     * @param mixed|null $value
     * @param string|integer $key
     */
    static public function prepend(array &$array, $value = null, $key = null){
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * @param array $array
     * @param string|integer $key
     * @param mixed $value
     */
    static public function set(array &$array, $key, $value){
        if (array_key_exists($key, $array)){
            $array[$key] = $value;
            return;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return;
    }

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param array $array
     * @param string|integer $key
     * @param mixed $value
     */
    static public function add(array &$array, $key, $value){
        if (!static::has($array, $key)){
            static::set($array, $key, $value);
        }
    }

    /**
     * Set all empty elements to the specified default value.
     *
     * @param array $array
     * @param null $default
     */
    static public function defaults(array &$array, $default = null){
        foreach ($array as $key => $value){
            if (empty($value)){
                $array[$key] = $default;
            }
        }
    }

    /**
     * Remove item/items from the array by key/keys
     * using "dot" notation.
     *
     * @param array $array
     * @param string|string[]|int|int[] $keys
     * @return void
     */
    static public function remove(array &$array, $keys){
        static::pull($array, $keys);
    }

    /**
     * Retrieve a new array with the results of calling a provided function
     * on every element in the array.
     *
     * @param array $array
     * @param Closure $callable
     * @param null $context
     * @return array
     */
    static public function map(array $array, Closure $callable, $context = null){
        if (!is_null($context)){
            $callable = $callable->bindTo($context, $context);
        }

        foreach ($array as $key => $value){
            $array[$key] = $callable($value, $key);
        }

        return $array;
    }

    /**
     * Executes a provided function once for each array element.
     *
     * @param array $array
     * @param Closure $callable
     * @param null $context
     */
    static public function each(array $array, Closure $callable, $context = null){
        static::map($array, $callable, $context);
    }

    /**
     * Retrieve a new array with all elements that pass the test implemented
     * by the provided function.
     *
     * @param array $array
     * @param Closure $filter
     * @param null $context
     * @return array
     */
    static public function filter(array $array, Closure $filter, $context = null){
        if (!is_null($context)){
            $filter = $filter->bindTo($context, $context);
        }

        $result = [];

        foreach ($array as $key => $value){
            if ($filter($value, $key) !== false){
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param array $array
     * @return array
     */
    static public function divide(array $array){
        return [array_keys($array), array_values($array)];
    }

    /**
     * Returns a new array containing all the values of the current array that are missing
     * in all transferred arrays.
     *
     * @param array $array
     * @param array|array[] ...$arrays
     * @return array
     */
    static public function diff(array $array, array ...$arrays){
        foreach ($array as $key => $value){
            foreach ($arrays as $item){
                if (in_array($value, $item, true)){
                    unset($array[$key]);
                    break;
                }
            }
        }

        return $array;
    }

    /**
     * Returns a new array containing all the values of the current array that are present
     * in all transferred arrays.
     *
     * @param array $array
     * @param array|array[] ...$arrays
     * @return array
     */
    static public function intersect(array $array, array ...$arrays){
        foreach ($array as $key => $value){
            foreach ($arrays as $item){
                if (!in_array($value, $item, true)){
                    unset($array[$key]);
                    break;
                }
            }
        }

        return $array;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param array $array
     * @return array
     */
    static public function collapse(array $array){
        $result = [];
        foreach ($array as $key => $item) {
            if (is_array($item)){
                $result = array_merge($result, $item);
                continue;
            }
            $result[$key] = $item;
        }
        return $result;
    }

    /**
     * Removes false values from an array.
     *
     * @param array $array
     * @return array
     */
    static public function compact(array $array){
        $result = [];
        foreach ($array as $key => $value){
            if (!empty($value)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Removes duplicate values from an array
     *
     * @param array $array
     * @return array
     */
    static public function unique(array $array){
        $result = [];
        foreach ($array as $key => $value){
            if(!in_array($value, $result, true)){
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Retrieve a new array with unique elements of all transferred arrays.
     * The order of the elements will be determined by the order of their appearance
     * in the source arrays.
     *
     * @param array|array[] ...$arrays
     * @return array
     */
    static public function union(array ...$arrays){
        return static::unique(array_merge(...$arrays));
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  mixed  $default
     * @param  Closure $callback
     * @return mixed
     */
    static public function first($array, $default = null, $callback = null){
        if ($default instanceof Closure){
            $callback = $default;
            $default = null;
        }

        if (is_null($callback)) {
            if (empty($array)) {
                return $default;
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key) !== false) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  mixed  $default
     * @param  Closure $callback
     * @return mixed
     */
    static public function last($array, $default = null, $callback = null){
        if ($default instanceof Closure){
            $callback = $default;
            $default = null;
        }

        if (is_null($callback)) {
            return empty($array) ? $default : end($array);
        }

        return static::first(array_reverse($array, true), $default, $callback);
    }

    /**
     * Returns all elements of the array except the last.
     * Pass the "Number" parameter to exclude more elements from the ending of the array.
     *
     * @param array $array
     * @param int $count
     * @return array
     */
    static public function initial(array $array, $count = 1){
        return array_slice($array, 0, max(0, count($array) - (int) $count), true);
    }

    /**
     * Returns all elements of the array except the first.
     * Pass the "Number" parameter to exclude more elements from the beginning of the array.
     *
     * @param array $array
     * @param int $count
     * @return array
     */
    static public function tail(array $array, $count = 1){
        return array_slice($array, (int) $count, null, true);
    }

    /**
     * Pluck an array of values from the current array by key/keys
     * using "dot" notation.
     *
     * @param array $array
     * @param string|string[]|int|int[] $keys
     * @return array
     */
    static public function pluck(array $array, $keys){
        return static::map($array, function($value) use ($keys){
            if (!is_array($value)){
                if (is_array($keys)){
                    $result = [];
                    foreach (array_keys($keys) as $key){
                        $result[$key] = null;
                    }
                    return $result;
                }
                return null;
            }
            return static::get($value, $keys);
        });
    }

    /**
     * Splits an array into groups, united by the result of calling the "condition" function
     * for each element of the array. To group by the value of an array element, in the "condition" parameter,
     * specify the element key ("dot" notation) for which you want to group.
     *
     * @param array $array
     * @param Closure|string|int $condition
     * @return array
     */
    static public function group(array $array, $condition){
        if ($condition instanceof Closure){
            $result = [];
            foreach ($array as $key => $value){
                $groupKey = call_user_func($condition, $value, $key);
                $groupKey = (is_string($groupKey) or is_numeric($groupKey)) ? $groupKey : gettype($groupKey);
                $result[$groupKey][$key] = $value;
            }
            return $result;
        }

        if (is_string($condition) or is_numeric($condition)){
            $result = [];
            foreach ($array as $key => $value){
                if (is_array($value)){
                    $groupKey = static::get($value, $condition);
                    $groupKey = (is_string($groupKey) or is_numeric($groupKey)) ? $groupKey : gettype($groupKey);
                } else {
                    $groupKey = gettype(null);
                }
                $result[$groupKey][$key] = $value;
            }
            return $result;
        }

        return $array;
    }

    /**
     * Filter items by the given key value pair using "dot" notation.
     *
     * The filter can be set in one of the following ways:
     * - Arr::where($array, 'key', '=', 'value');
     * - Arr::where($array, 'key', 'value');
     * - Arr::where($array, [['key', '=', 'value'],['key_2', '=', 'value_2']]);
     * - Arr::where($array, [['key', 'value'],['key_2', 'value_2']]);
     * - Arr::where($array, 'key');
     *
     * The filter can use the following comparison operators:
     * - "=","=="   : Equality
     * - "==="      : Strict equality
     * - "!=","<>"  : Inequality
     * - "!=="      : Strict inequality
     * - ">="       : More or equal
     * - "<="       : Less or equal
     * - "<"        : More
     * - ">"        : Less
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     * @return array
     */
    static public function where(array $array, $key, $operator = null, $value = null){
        $numArgs = func_num_args();
        $conditions = [];

        if ($numArgs === 2){
            if (is_array($key)){
                foreach ($key as $item){
                    if (!is_array($item)){
                        continue;
                    }

                    $numArgs = count($item);

                    if ($numArgs === 2){
                        $conditions[] = [$item[0], '=', $item[1]];
                    } elseif ($numArgs === 3){
                        $conditions[] = [$item[0], $item[1], $item[2]];
                    } else {
                        continue;
                    }
                }
            } else {
                $conditions[] = [$key, '=', true];
            }
        } elseif ($numArgs === 3){
            $conditions[] = [$key, '=', $operator];
        } else {
            $conditions[] = [$key, $operator, $value];
        }

        if (empty($conditions)){
            return [];
        }

        return static::filter($array, function($item) use ($conditions){
            if (!is_array($item)){
                return false;
            }

            foreach ($conditions as $condition){

                $value = static::get($item, $condition[0]);

                switch ($condition[1]) {
                    default:
                    case '=':
                    case '==':  $result = $condition[2] == $value; break;
                    case '!=':
                    case '<>':  $result = $condition[2] != $value; break;
                    case '<':   $result = $condition[2] < $value; break;
                    case '>':   $result = $condition[2] > $value; break;
                    case '<=':  $result = $condition[2] <= $value; break;
                    case '>=':  $result = $condition[2] >= $value; break;
                    case '===': $result = $condition[2] === $value; break;
                    case '!==': $result = $condition[2] !== $value; break;
                }

                if (!$result){
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Retrieve a new array of items from the current array that are equal
     * to one of the passed values using "dot" notation.
     *
     * @param array $array
     * @param string|int $key
     * @param array|string|int $values
     * @param bool $strict
     * @return array
     */
    static public function whereIn(array $array, $key, $values, $strict = false){
        $values = Arr::wrap($values);
        return static::filter($array, function($item) use ($key, $values, $strict){
            if (is_array($item) and static::has($item, $key)){
                return in_array(static::get($item, $key), $values, $strict);
            }
            return false;
        });
    }

    /**
     * Retrieve the new array of items from the current array that are not equal
     * to all specified values using the dot notation.
     *
     * @param array $array
     * @param string|int $key
     * @param array|string|int $values
     * @param bool $strict
     * @return array
     */
    static public function whereNotIn(array $array, $key, $values, $strict = false){
        $values = Arr::wrap($values);
        return static::filter($array, function($item) use ($key, $values, $strict){
            if (is_array($item)){
                if (!static::has($item, $key)){
                    return true;
                }
                return !in_array(static::get($item, $key), $values, $strict);
            }
            return false;
        });
    }

    /**
     * Determine if all items in the array pass the given test.
     *
     * The test can be set in one of the following ways:
     * - Arr::every($array, function($value, $key){return true;})
     * - Arr::every($array, 'key', function($value, $key){return true;})
     * - Arr::every($array, 'key', '=', 'value')
     *
     * @see Arr::where()
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    static public function every(array $array, $key, $operator = null, $value = null){
        if (empty($array)){
            return false;
        }

        if ($key instanceof Closure){
            return count($array) === count(Arr::filter($array, $key));
        }

        if ($operator instanceof Closure){
            return count($array) === count(Arr::filter(Arr::pluck($array, $key), $operator));
        }

        return count($array) === count(static::where(...func_get_args()));
    }

    /**
     * Find highest value.
     *
     * @param array $array
     * @param string|int|null $key
     * @return mixed
     */
    static public function max(array $array, $key = null){
        if (!is_null($key)){
            $array = static::pluck($array, $key);
        }
        return max($array);
    }

    /**
     * Find lowest value.
     *
     * @param array $array
     * @param string|int|null $key
     * @return mixed
     */
    static public function min(array $array, $key = null){
        if (!is_null($key)){
            $array = static::pluck($array, $key);
        }
        return min($array);
    }

    /**
     * Find average value.
     *
     * @param array $array
     * @param string|int|null $key
     * @return float|int
     */
    static public function avg(array $array, $key = null){
        if (!is_null($key)){
            $array = static::pluck($array, $key);
        }

        $sum = 0;
        $count = count($array);

        foreach ($array as $item){
            if (!is_numeric($item)){
                continue;
            }
            $sum += $item;
        }

        return $sum > 0 ? $sum / $count : $sum;
    }

    /**
     * Retrieve sum of values.
     *
     * @param array $array
     * @param string|int|null $key
     * @return float|int
     */
    static public function sum(array $array, $key = null){
        if (!is_null($key)){
            $array = static::pluck($array, $key);
        }

        $sum = 0;

        foreach ($array as $item){
            if (!is_numeric($item)){
                continue;
            }
            $sum += $item;
        }

        return $sum;
    }
}
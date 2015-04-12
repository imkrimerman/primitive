<?php namespace im\Primitive\Support;

use Exception;

/**
 * Class Arr
 *
 * @package im\Primitive\Support
 * @author Taylor Otwell | refactored Igor Krimerman <i.m.krimerman@gmail.com>
 */
class Arr {

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $value
     *
     * @return array
     */
    public static function add($array, $key, $value)
    {
        if (is_null(static::get($array, $key)))
        {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array $array
     *
     * @return array
     */
    public static function divide($array)
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array  $array
     * @param  string $prepend
     *
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            }
            else
            {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param  array        $array
     * @param  array|string $keys
     *
     * @return array
     */
    public static function except($array, $keys)
    {
        return array_diff_key($array, array_flip((array) $keys));
    }

    /**
     * Fetch a flattened array of a nested array element.
     *
     * @param  array  $array
     * @param  string $key
     *
     * @return array
     */
    public static function fetch($array, $key)
    {
        $results = [];

        foreach (explode('.', $key) as $segment)
        {
            foreach ($array as $value)
            {
                if (array_key_exists($segment, $value = (array) $value))
                {
                    $results[] = $value[$segment];
                }
            }

            $array = array_values($results);
        }

        return array_values($results);
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array    $array
     * @param  callable $callback
     * @param  mixed    $default
     *
     * @return mixed
     */
    public static function first($array, $callback, $default = null)
    {
        foreach ($array as $key => $value)
        {
            if (call_user_func($callback, $key, $value)) return $value;
        }

        return value($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array    $array
     * @param  callable $callback
     * @param  mixed    $default
     *
     * @return mixed
     */
    public static function last($array, $callback, $default = null)
    {
        return static::first(array_reverse($array), $callback, $default);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array $array
     *
     * @return array
     */
    public static function flatten($array)
    {
        $return = [];

        array_walk_recursive($array, function ($x) use (&$return) { $return[] = $x; });

        return $return;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array|object $array
     * @param  array|string $keys
     *
     * @return void
     */
    public static function forget(&$array, $keys)
    {
        $original =& $array;

        foreach ((array) $keys as $key)
        {
            $parts = explode('.', $key);

            while (count($parts) > 1)
            {
                $part = array_shift($parts);

                if ((is_array($array) && isset($array[$part]) && is_array($array[$part])) ||
                    (is_object($array) && isset($array->{$part}) && is_array($array->{$part})))
                {
                    if (is_array($array)) $array =& $array[$part];
                    elseif (is_object($array)) $array =& $array->{$part};
                }
            }

            $part = array_shift($parts);

            if (is_array($array)) unset($array[$part]);
            elseif (is_object($array)) unset($array->{$part});

            // clean up after each pass
            $array =& $original;
        }
    }

    /**
     * Get an item using "dot" notation.
     *
     * @param  array|object  $array
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        return _data_get($array, $key, $default);
    }

    /**
     * Check if an item exists using "dot" notation.
     *
     * @param  array|object  $array
     * @param  string $key
     *
     * @return bool
     */
    public static function has($array, $key)
    {
        if (empty($array) || is_null($key)) return false;

        if (array_key_exists($key, $array)) return true;

        return _data_get($array, $key, new Exception('Not Found')) instanceof Exception ? false : true;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array        $array
     * @param  array|string $keys
     *
     * @return array
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  array  $array
     * @param  string $value
     * @param  string $key
     *
     * @return array
     */
    public static function pluck($array, $value, $key = null)
    {
        $results = [];

        foreach ($array as $item)
        {
            if (isset($item[$value]) || isset($item->{$value}))
            {
                $itemValue = is_object($item) ? $item->{$value} : $item[$value];

                // If the key is "null", we will just append the value to the array and keep
                // looping. Otherwise we will key the array using the value of the key we
                // received from the developer. Then we'll return the final array form.
                if (is_null($key))
                {
                    $results[] = $itemValue;
                }
                else
                {
                    $itemKey = is_object($item) ? $item->{$key} : $item[$key];

                    $results[$itemKey] = $itemValue;
                }
            }
        }

        return $results;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Set item to a given key using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array|object  $array
     * @param  string $key
     * @param  mixed  $value
     *
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if ((is_array($array) && ( ! isset($array[$key]) || ! is_array($array[$key]))) ||
                (is_object($array) && ( ! isset($array->{$key}) || ! is_array($array->{$key}))))
            {
                if (is_array($array)) $array[$key] = [];
                elseif (is_object($array)) $array->{$key} = [];
            }

            if (is_array($array)) $array =& $array[$key];
            elseif (is_object($array)) $array =& $array->{$key};
        }

        $key = array_shift($keys);

        if (is_array($array)) $array[$key] = $value;
        elseif (is_object($array)) $array->{$key} = $value;

        return $array;
    }

    /**
     * Search value in Traversable and return key using dot notation.
     *
     * @param $array
     * @param $value
     * @param bool $strict
     * @param string $prepend
     * @param null $default
     * @return int|null|string
     */
    public static function search($array, $value, $strict = false, $prepend = '', $default = null)
    {
        foreach ($array as $key => $_value_)
        {
            $key = $prepend === '' ? $key : $prepend.'.'.$key;

            if (($strict && $_value_ === $value) || ( ! $strict && $_value_ == $value))
            {
                return $key;
            }

            if (is_array($_value_) || $_value_ instanceof \Traversable)
            {
                $found = static::search($_value_, $value, $strict, $key, $default);

                if ($found === $default) continue;

                return $found;
            }
        }

        return $default;
    }

    /**
     * Check if not is array or not key exists.
     *
     * @param $array
     * @param $segment
     * @return bool
     */
    protected static function isNotArrayOrNotKeyExists($array, $segment)
    {
        return ! is_array($array) || ! array_key_exists($segment, $array);
    }
}

<?php

use Primitive\Support\Dump\Dumper;

if ( ! function_exists('a'))
{
    /**
     * @param array $array
     * @return \im\Primitive\Container\Container
     */
    function a($array = [])
    {
        return new im\Primitive\Container\Container($array);
    }
}

if ( ! function_exists('s'))
{
    /**
     * @param string $string
     * @return \im\Primitive\String\String
     */
    function s($string = '')
    {
        return new im\Primitive\String\String($string);
    }
}

if ( ! function_exists('value'))
{
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value
     * @return mixed
     */
    function value($value)
    {
        return is_callable($value) ? $value() : $value;
    }
}

if ( ! function_exists('first'))
{
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array $array
     * @return mixed
     */
    function first($array)
    {
        return reset($array);
    }
}

if ( ! function_exists('firstKey'))
{
    /**
     * Get the first elements key of an array. Useful for method chaining.
     *
     * @param  array $array
     * @return mixed
     */
    function firstKey($array)
    {
        reset($array);

        return key($array);
    }
}

if ( ! function_exists('last'))
{
    /**
     * Get the last element from an array.
     *
     * @param  array $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if ( ! function_exists('lastKey'))
{
    /**
     * Get the last elements key from an array.
     *
     * @param  array $array
     * @return mixed
     */
    function lastKey($array)
    {
        end($array);

        return key($array);
    }
}

if ( ! function_exists('flipVars'))
{
    /**
     * Flips 2 variables
     *
     * @param $a
     * @param $b
     */
    function flipVars(& $a, & $b)
    {
        $a ^= $b ^= $a ^= $b;
    }
}

if ( ! function_exists('measureObject'))
{
    /**
     * Count object length
     *
     * @param $object
     *
     * @return int
     */
    function measureObject($object)
    {
        if (is_object($object))
        {
            $reflactor = new ReflectionClass($object);

            $properties = $reflactor->getProperties();
            $constants = $reflactor->getConstants();
            $methods = $reflactor->getMethods();

            return count($properties) + count($constants) + count($methods);
        }

        return 0;
    }
}

if ( ! function_exists('strReplaceArray'))
{
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string $search
     * @param  array $replace
     * @param  string $subject
     * @return string
     */
    function strReplaceArray($search, array $replace, $subject)
    {
        foreach ($replace as $value)
        {
            $subject = preg_replace('/' . $search . '/', $value, $subject, 1);
        }

        return $subject;
    }
}

if ( ! function_exists('objectGet'))
{
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object $object
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function objectGet($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') return $object;

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_object($object) || ! isset($object->{$segment}))
            {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if ( ! function_exists('objectHasMethod'))
{
    /**
     * Check if object has method
     *
     * @param $object
     * @param $method
     *
     * @return bool
     */
    function objectHasMethod($object, $method)
    {
        $reflactor = new ReflectionClass($object);

        foreach ($reflactor->getMethods() as $reflactorMethod)
        {
            if ($reflactorMethod->getName() == $method)
            {
                return true;
            }
        }

        return false;
    }
}

if ( ! function_exists('objectMethodHasAcceptableParameters'))
{
    /**
     * @param $object
     * @param $method
     *
     * @return bool
     */
    function objectMethodHasAcceptableParameters($object, $method)
    {
        $reflactor = new ReflectionClass($object);

        foreach ($reflactor->getMethods() as $reflactorMethod)
        {
            if ($reflactorMethod->getName() == $method && $reflactorMethod->getNumberOfRequiredParameters() == 0)
            {
                return true;
            }
        }

        return false;
    }
}

if ( ! function_exists('dataGet'))
{
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed $target
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function dataGet($target, $key, $default = null)
    {
        if (is_null($key)) return $target;

        foreach (explode('.', $key) as $segment)
        {
            if (is_array($target))
            {
                if ( ! array_key_exists($segment, $target))
                {
                    return value($default);
                }

                $target = $target[$segment];
            }
            elseif (is_object($target))
            {
                if ( ! isset($target->{$segment}))
                {
                    return value($default);
                }

                $target = $target->{$segment};
            }
            else
            {
                return value($default);
            }
        }

        return $target;
    }
}

if ( ! function_exists('e'))
{
    /**
     * Escape HTML entities in a string.
     *
     * @param  string $value
     * @return string
     */
    function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}

if ( ! function_exists('dd'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {
        array_map(function($x) { (new Dumper)->dump($x); }, func_get_args()); die;
    }
}


if ( ! function_exists('d'))
{
    /**
     * Dump the passed variables.
     *
     * @param  mixed
     * @return void
     */
    function d()
    {
        array_map(function($x) { (new Dumper)->dump($x); }, func_get_args());
    }
}

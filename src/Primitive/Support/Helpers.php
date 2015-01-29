<?php

use im\Primitive\Support\Dump\Dumper;
use im\Primitive\Container\Container;
use im\Primitive\Container\RevertableContainer;
use im\Primitive\Container\ContainerType;
use im\Primitive\String\String;
use im\Primitive\Int\Int;
use im\Primitive\Float\Float;
use im\Primitive\Bool\Bool;


if ( ! function_exists('container'))
{
    /**
     * @param mixed $array
     * @param int   $type
     *
     * @return \im\Primitive\Container\Container
     */
    function container($array = [], $type = ContainerType::SIMPLE)
    {
        if ($type === ContainerType::SIMPLE)
        {
            return new Container($array);
        }
        elseif ($type === ContainerType::REVERTABLE)
        {
            return new RevertableContainer($array);
        }
    }
}

if ( ! function_exists('string'))
{
    /**
     * @param string $string
     * @return \im\Primitive\String\String
     */
    function string($string = '')
    {
        return new String($string);
    }
}

if ( ! function_exists('int'))
{
    /**
     * @param     $value
     *
     * @return \im\Primitive\Int\Int
     */
    function int($value)
    {
        return new Int($value);
    }
}

if ( ! function_exists('float'))
{
    /**
     * @param     $value
     *
     * @return \im\Primitive\Float\Float
     */
    function float($value)
    {
        return new Float($value);
    }
}

if ( ! function_exists('bool'))
{
    /**
     * @param      $value
     *
     * @return \im\Primitive\Bool\Bool
     */
    function bool($value)
    {
        return new Bool($value);
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

if ( ! function_exists('first_key'))
{
    /**
     * Get the first elements key of an array. Useful for method chaining.
     *
     * @param  array $array
     * @return mixed
     */
    function first_key($array)
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

if ( ! function_exists('last_key'))
{
    /**
     * Get the last elements key from an array.
     *
     * @param  array $array
     * @return mixed
     */
    function last_key($array)
    {
        end($array);

        return key($array);
    }
}

if ( ! function_exists('flip_integers'))
{
    /**
     * Flips 2 integers
     *
     * @param $a
     * @param $b
     */
    function flip_integers(& $a, & $b)
    {
        $a ^= $b ^= $a ^= $b;
    }
}

if ( ! function_exists('flip_vars'))
{
    /**
     * Flips 2 variables
     *
     * @param $a
     * @param $b
     *
     * @return array
     */
    function flip_vars($a, $b)
    {
        return [$b, $a];
    }
}

if ( ! function_exists('factorial_recursive'))
{
    /**
     * Calculate Factorial Recursively
     *
     * @param $value
     *
     * @return int
     */
    function factorial_recursive($value)
    {
        return $value ? $value * factorial($value - 1) : 1;
    }
}


if ( ! function_exists('factorial'))
{
    /**
     * Calculate Factorial
     *
     * @param $value
     *
     * @return int
     * @throws \InvalidArgumentException
     */
    function factorial($value)
    {
        if ($value < 0)
        {
            throw new InvalidArgumentException('Number cannot be less than zero');
        }

        $factorial = 1;

        while ($value > 0)
        {
            $factorial *= $value;
            --$value;
        }

        return $factorial;
    }
}

if ( ! function_exists('str_replace_array'))
{
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param  string $search
     * @param  array $replace
     * @param  string $subject
     * @return string
     */
    function str_replace_array($search, array $replace, $subject)
    {
        foreach ($replace as $value)
        {
            $subject = preg_replace('/' . $search . '/', $value, $subject, 1);
        }

        return $subject;
    }
}

if ( ! function_exists('measure_object'))
{
    /**
     * Count object length
     *
     * @param $object
     *
     * @return int
     */
    function measure_object($object)
    {
        if (is_object($object))
        {
            $reflactor = new ReflectionClass($object);

            $properties = $reflactor->getProperties();
            $constants = $reflactor->getConstants();

            return count($properties) + count($constants);
        }

        return 0;
    }
}

if ( ! function_exists('object_get'))
{
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object $object
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function object_get($object, $key, $default = null)
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

if ( ! function_exists('data_get'))
{
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed $target
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
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

if ( ! function_exists('ddump'))
{
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function ddump()
    {
        array_map(function($x) { (new Dumper)->dump($x); }, func_get_args()); die;
    }
}


if ( ! function_exists('dump'))
{
    /**
     * Dump the passed variables.
     *
     * @param  mixed
     * @return void
     */
    function dump()
    {
        array_map(function($x) { (new Dumper)->dump($x); }, func_get_args());
    }
}

<?php

/**
 * @param array $array
 * @return \im\Primitive\Container\Container
 */
function a($array = [])
{
    return new im\Primitive\Container\Container($array);
}

/**
 * @param string $string
 * @return \im\Primitive\String\String
 */
function s($string = '')
{
    return new im\Primitive\String\String($string);
}

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


/**
 * Dump the passed variables and end the script.
 *
 * @param  mixed
 * @return void
 */
function dd()
{
    array_map(function($x) { var_dump($x); }, func_get_args()); die;
}

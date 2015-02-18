<?php namespace im\Primitive\Support\Abstracts;

use Countable;
use ArrayAccess;
use IteratorAggregate;
use im\Primitive\Support\Contracts\ArrayableContract;
use im\Primitive\Support\Contracts\FileableContract;
use im\Primitive\Support\Contracts\JsonableContract;
use im\Primitive\Support\Exceptions\OffsetNotExistsException;
use im\Primitive\Support\Iterators\RecursiveContainerIterator;

/**
 * Class ComplexType
 *
 * @package im\Primitive\Support\Abstracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
abstract class ComplexType extends Type implements
    FileableContract, JsonableContract, ArrayableContract, ArrayAccess, IteratorAggregate, Countable {

    /**
     * Write json representation of type value to file
     * You can specify second argument to call json_encode with params
     *
     * @param $file
     * @param int|IntegerContract $jsonOptions
     * @return bool
     */
    public function toFile($file, $jsonOptions = 0)
    {
        if (is_dir(pathinfo($file, PATHINFO_DIRNAME)))
        {
            return (bool) file_put_contents($file, $this->toJson($this->getIntegerable($jsonOptions)));
        }

        return false;
    }

    /**
     * Construct Type from file
     *
     * @param string|StringContract $file
     * @return mixed
     */
    abstract public function fromFile($file);

    /**
     * Return value converted to Json
     *
     * @param int|IntegerContract $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->value(), $this->getIntegerable($options));
    }

    /**
     * Construct Type from json.
     *
     * @param string|StringContract $json
     * @return mixed
     */
    abstract public function fromJson($json);

    /**
     * Return converted Type to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($item)
        {
            return $this->getArrayable($item, $item);

        }, $this->value());
    }

    /**
     * Construct from array
     *
     * @param array $array
     * @return $this
     */
    public function fromArray(array $array = [])
    {
        return $this->initialize($array);
    }

    /**
     * Get value by specified $key.
     *
     * @param string|StringContract $key
     * @param null|mixed $default
     * @return mixed
     */
    abstract public function get($key, $default = null);

    /**
     * Check if Type has specified $key.
     *
     * @param string|StringContract $key
     * @return mixed
     */
    abstract public function has($key);

    /**
     * Forget value by specified $key.
     *
     * @param string|StringContract $key
     * @return mixed
     */
    abstract public function forget($key);

    /**
     * {@inheritdoc}
     */
    abstract public function length();

    /*
    |--------------------------------------------------------------------------
    | ArrayAccess
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     * @throws \im\Primitive\Support\Exceptions\OffsetNotExistsException
     */
    public function offsetGet($offset)
    {
        if ($this->has($offset))
        {
            return $this->get($offset);
        }

        throw new OffsetNotExistsException('Offset: ' . $offset . ' not exists.');
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @return void
     */
    abstract public function offsetSet($offset, $value);

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing Iterator or
     */
    public function getIterator()
    {
        return new RecursiveContainerIterator($this->value());
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of a Type
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->length();
    }
}

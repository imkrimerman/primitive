<?php namespace im\Primitive\Support\Abstracts;

use Countable;
use ArrayAccess;
use JsonSerializable;
use IteratorAggregate;
use im\Primitive\Support\Traits\RetrievableTrait;
use im\Primitive\Support\Contracts\ArrayableContract;
use im\Primitive\Support\Contracts\FileableContract;
use im\Primitive\Support\Contracts\JsonableContract;
use im\Primitive\Support\Exceptions\OffsetNotExistsException;
use im\Primitive\Support\Iterators\RecursiveContainerIterator;


abstract class ComplexType extends Type implements FileableContract, JsonableContract, JsonSerializable, ArrayableContract, ArrayAccess, IteratorAggregate, Countable {

    use RetrievableTrait;

    /**
     * Write json representation of type value to file
     *
     * You can specify second argument to call json_encode with params
     *
     * @param $file
     * @param int $jsonOptions
     * @return bool
     */
    public function toFile($file, $jsonOptions = 0)
    {
        if (is_dir(pathinfo($file, PATHINFO_DIRNAME)))
        {
            return (bool) file_put_contents($file, $this->toJson($jsonOptions));
        }

        return false;
    }

    /**
     * Construct Type from file
     *
     * @param $file
     * @return mixed
     */
    abstract public function fromFile($file);

    /**
     * Return value converted to Json
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->value(), $options);
    }

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
     *
     * @return $this
     */
    public function fromArray(array $array = [])
    {
        return $this->initialize($array);
    }

    abstract public function get($key, $default = null);

    abstract public function has($key);

    abstract public function forget($key);

    abstract public function length();

    /*
    |--------------------------------------------------------------------------
    | ArrayAccess
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
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
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    abstract public function offsetSet($offset, $value);

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
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
     * @return RecursiveContainerIterator
     */
    public function getIterator()
    {
        return new RecursiveContainerIterator($this->value());
    }

    /*
    |--------------------------------------------------------------------------
    | JsonSerializable
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    abstract function jsonSerialize();

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->length();
    }
}

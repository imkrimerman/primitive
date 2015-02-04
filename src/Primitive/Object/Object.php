<?php namespace im\Primitive\Object;

use Countable;
use ArrayAccess;
use Traversable;
use IteratorAggregate;
use JsonSerializable;

use im\Primitive\String\String;
use im\Primitive\Container\Container;
use im\Primitive\Support\Arr;
use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Contracts\ObjectInterface;
use im\Primitive\Support\Contracts\FileableInterface;
use im\Primitive\Support\Contracts\JsonableInterface;
use im\Primitive\Support\Contracts\ArrayableInterface;
use im\Primitive\Support\Exceptions\OffsetNotExistsException;
use im\Primitive\Support\Traits\RetrievableTrait;


class Object extends Type implements ObjectInterface, JsonSerializable, JsonableInterface, ArrayAccess, ArrayableInterface, FileableInterface, IteratorAggregate, Countable {

    use RetrievableTrait;

    /**
     * @param mixed $from
     */
    public function __construct($from = [])
    {
        $this->initialize($from);
    }

    /**
     * @param $field
     * @param $value
     *
     * @return \im\Primitive\Object\Object
     */
    public function __set($field, $value)
    {
        return $this->set($field, $value);
    }

    /**
     * @param $property
     * @return mixed|null
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * @param       $property
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function get($property, $default = null)
    {
        if ($this->has($property))
        {
            return $this->retrieveValue($property);
        }

        return $default;
    }

    /**
     * @param $property
     * @param $value
     *
     * @return $this
     */
    public function set($property, $value)
    {
        Arr::set($this, $this->getStringable($property), $value);

        return $this;
    }

    /**
     * @param $property
     *
     * @return bool
     */
    public function has($property)
    {
        return Arr::has($this, $this->getStringable($property));
    }

    /**
     * @param $property
     *
     * @return $this
     */
    public function forget($property)
    {
        Arr::forget($this, $property);

        return $this;
    }

    /**
     * @param $from
     *
     * @return $this
     */
    protected function initialize($from)
    {
        $array = (new Container($from))->notNumericKeys();

        foreach ($array as $property => $value)
        {
            $this->{$property} = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function value()
    {
        return get_object_vars($this);
    }

    /**
     * @return int
     */
    public function length()
    {
        return $this->toContainer()->length();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return new String($this->toJson());
    }

    /**
     * @return \im\Primitive\Container\Container
     */
    public function toContainer()
    {
        return new Container($this->value());
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return $this->toContainer()->toJson();
    }

    /**
     * @param $json
     *
     * @return \im\Primitive\Object\Object
     */
    public function fromJson($json)
    {
        return $this->initialize($json);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->value();
    }

    /**
     * @param array $array
     *
     * @return \im\Primitive\Object\Object
     */
    public function fromArray(array $array = [])
    {
        return $this->initialize($array);
    }

    /**
     * @param $file
     * @return bool
     */
    public function toFile($file)
    {
        if (is_dir(pathinfo($file, PATHINFO_DIRNAME)))
        {
            return (bool) file_put_contents($file, $this->toJson());
        }

        return false;
    }

    /**
     * @param $file
     * @return $this
     */
    public function fromFile($file)
    {
        return $this->initialize($file);
    }

    /**
     * @param $property
     *
     * @return mixed
     */
    protected function retrieveValue($property)
    {
        return data_get($this, $property);
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
    function jsonSerialize()
    {
        return $this->toContainer()->jsonSerialize();
    }

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
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

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
    | Countable
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return $this->length();
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *       <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->toContainer()->getIterator();
    }
}

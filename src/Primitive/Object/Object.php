<?php namespace im\Primitive\Object;

use ArrayAccess;
use Countable;
use im\Primitive\Support\Iterators\RecursiveContainerIterator;
use IteratorAggregate;
use JsonSerializable;

use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Contracts\ObjectInterface;
use im\Primitive\Support\Contracts\JsonableInterface;
use im\Primitive\Support\Contracts\ArrayableInterface;
use im\Primitive\Support\Exceptions\OffsetNotExistsException;
use Traversable;


class Object extends Type implements ObjectInterface, JsonSerializable, JsonableInterface, ArrayAccess, ArrayableInterface, IteratorAggregate, Countable {

    /**
     * @param $from
     */
    public function __construct($from = [])
    {
        $this->initialize($from);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function __get($value)
    {
        if (method_exists($this, $value))
        {
            return $this->{$value}();
        }
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
     * @param      $field
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($field, $default = null)
    {
        if ($this->has($field))
        {
            return $this->retrieveValue($field);
        }

        return $default;
    }

    /**
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function set($field, $value)
    {
        $this->{$field} = $value;

        return $this;
    }

    /**
     * @param $field
     *
     * @return bool
     */
    public function has($field)
    {
        return isset($this->{$field});
    }

    /**
     * @param $from
     *
     * @return $this
     */
    protected function initialize($from)
    {
        foreach (container($from)->notNumericKeys() as $field => $value)
        {
            $this->{$field} = $value;
        }

        return $this;
    }

    /**
     * @return \im\Primitive\Container\Container
     */
    public function value()
    {
        return container(get_object_vars($this));
    }

    /**
     * @param $field
     *
     * @return mixed
     */
    protected function retrieveValue($field)
    {
        return value($this->{$field});
    }

    /**
     * @return int
     */
    public function length()
    {
        return measure_object($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return $this->value()->toJson();
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
        return $this->value()->toArray();
    }

    /**
     * @param array $array
     *
     * @return \im\Primitive\Object\Object
     */
    public function fromArray(array $array = array())
    {
        return $this->initialize($array);
    }

    /*
    |--------------------------------------------------------------------------
    | JsonSerializable
    |--------------------------------------------------------------------------
    */

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->value()->jsonSerialize();
    }

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
        return isset($this->{$offset});
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
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

        throw new OffsetNotExistsException('Offset: '.$offset.' not exists.');
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
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
        $this->{$offset} = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
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
        unset($this->{$offset});
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
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
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new RecursiveContainerIterator();
    }
}

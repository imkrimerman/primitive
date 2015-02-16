<?php namespace im\Primitive\Object;

use im\Primitive\String\String;
use im\Primitive\Container\Container;
use im\Primitive\Support\Arr;
use im\Primitive\Support\Abstracts\ComplexType;
use im\Primitive\Support\Contracts\ObjectContract;

/**
 * Class Object
 *
 * @package im\Primitive\Object
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class Object extends ComplexType implements ObjectContract {

    /**
     * Construct Object Type.
     *
     * @param mixed $from
     */
    public function __construct($from = [])
    {
        $this->initialize($from);
    }

    /**
     * Magic set method. Dynamically sets value.
     *
     * @param string|StringContract $property
     * @param mixed $value
     * @return \im\Primitive\Object\Object
     */
    public function __set($property, $value)
    {
        return $this->set($property, $value);
    }

    /**
     * Magic get method. Dynamically gets value.
     *
     * @param string|StringContract $property
     * @return mixed|null
     */
    public function __get($property)
    {
        return $this->get($property);
    }

    /**
     * Getter for value. Supports dot notation.
     * Can retrieve value from objects, arrays, Containers, instances of ArrayableContract and ObjectContract.
     *
     * @param string|StringContract $property
     * @param mixed $default
     * @return mixed|null
     */
    public function get($property, $default = null)
    {
        $property = $this->getStringable($property);

        if ($this->has($property))
        {
            return $this->retrieveValue($property);
        }

        return $default;
    }

    /**
     * Setter for value. Supports dot notation.
     * Can set value to objects, arrays, Containers, instances of ArrayableContract and ObjectContract
     * or create nested arrays (if dot notation is used) if was not set.
     *
     * @param string|StringContract $property
     * @param mixed $value
     * @return $this
     */
    public function set($property, $value)
    {
        Arr::set($this, $this->getStringable($property), $value);

        return $this;
    }

    /**
     * Check if Object has specified property. Supports dot notation.
     * Can check in objects, arrays, Containers, instances of ArrayableContract and ObjectContract.
     *
     * @param string|StringContract $property
     * @return bool
     */
    public function has($property)
    {
        return Arr::has($this, $this->getStringable($property));
    }

    /**
     * Forget specified property. Supports dot notation.
     * Can unset it in objects (public properties), arrays, Containers,
     * instances of ArrayableContract and ObjectContract.
     *
     * @param string|StringContract $property
     * @return $this
     */
    public function forget($property)
    {
        Arr::forget($this, $property);

        return $this;
    }

    /**
     * {@inheritdoc}
     * Can be constructed like Container Type.
     *
     * @param mixed $value
     * @return $this
     */
    protected function initialize($value)
    {
        $array = (new Container($value))->notNumericKeys();

        foreach ($array as $property => $value)
        {
            $this->{$property} = $value;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function value()
    {
        return get_object_vars($this);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function length()
    {
        return $this->toContainer()->length();
    }

    /**
     * Return string representation of Object Type (json).
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert Object Type to String Type.
     *
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return new String($this->toJson());
    }

    /**
     * Convert Object Type to Container Type.
     *
     * @return \im\Primitive\Container\Container
     */
    public function toContainer()
    {
        return new Container($this->value());
    }

    /**
     * Construct Object from json.
     *
     * @param $json
     * @return \im\Primitive\Object\Object
     */
    public function fromJson($json)
    {
        return $this->initialize($json);
    }

    /**
     * Construct Object from file.
     *
     * @param $file
     * @return $this
     */
    public function fromFile($file)
    {
        return $this->initialize($file);
    }

    /**
     * {@inheritdoc}
     * Retrieves with dot notation.
     *
     * @param string|StringContract $property
     * @return mixed
     */
    protected function retrieveValue($property)
    {
        return _data_get($this, $property, $this->getDefault());
    }

    /**
     * {@inheritdoc}
     *
     * @return null
     */
    protected function getDefault()
    {
        return null;
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
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset
     *      The offset to assign the value to.
     * @param mixed $value
     *      The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
}

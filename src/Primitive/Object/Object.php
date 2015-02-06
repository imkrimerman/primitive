<?php namespace im\Primitive\Object;

use im\Primitive\String\String;
use im\Primitive\Container\Container;
use im\Primitive\Support\Arr;
use im\Primitive\Support\Abstracts\ComplexType;
use im\Primitive\Support\Contracts\ObjectInterface;


class Object extends ComplexType implements ObjectInterface {

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
     * @param $json
     *
     * @return \im\Primitive\Object\Object
     */
    public function fromJson($json)
    {
        return $this->initialize($json);
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
}

<?php namespace im\Primitive\Support\Abstracts;

use Serializable;
use im\Primitive\Support\Contracts\TypeInterface;


abstract class Type implements TypeInterface, Serializable {

    /**
     * @param $value
     */
    abstract public function __construct($value);


    /**
     * @param $value
     *
     * @return $this
     */
    abstract protected function initialize($value);

    /**
     * @return mixed
     */
    abstract public function value();

    /**
     * @param $value
     *
     * @return mixed
     */
    abstract protected function retrieveValue($value);

    /**
     * @return string
     */
    abstract public function __toString();

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
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->value());
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->initialize(unserialize($serialized));
    }

    /**
     * Dump Type.
     *
     * Var dump
     *
     * @param bool $die
     */
    public function dump($die = false)
    {
        (new Dumper())->dump($this);

        if ($die) die;
    }

    /**
     * @param $value
     *
     * @return static
     */
    public function make($value)
    {
        return new static($value);
    }

    /**
     * @param $value
     *
     * @return static
     */
    public static function create($value)
    {
        return new static($value);
    }

    /**
     * @return number
     */
    public function __invoke()
    {
        return $this->value();
    }
}

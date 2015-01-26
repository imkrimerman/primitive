<?php namespace im\Primitive\Support\Abstracts;

use Serializable;
use im\Primitive\Support\Contracts\TypeInterface;


abstract class Type implements TypeInterface, Serializable {

    abstract protected function initialize($value);

    abstract public function value();

    abstract protected function retrieveValue($value);

    abstract protected function getDefault();

    abstract public function __destruct();

    abstract public function __toString();

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
     * @param $value
     *
     * @return static
     */
    public function make($value)
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

<?php namespace im\Primitive\Support\Abstracts;

use Serializable;
use im\Primitive\Support\Contracts\TypeInterface;


abstract class Number implements TypeInterface, Serializable {

    protected $value;

    abstract public function __construct($value);

    abstract protected function retrieveValue($value);

    abstract protected function getDefault();

    /**
     * @param $plus
     *
     * @return static
     */
    public function plus($plus)
    {
        return new static($this->value + $this->retrieveValue($plus));
    }

    /**
     * @param $minus
     *
     * @return static
     */
    public function minus($minus)
    {
        return new static($this->value - $this->retrieveValue($minus));
    }

    /**
     * @param $multiply
     *
     * @return static
     */
    public function multiply($multiply)
    {
        return new static($this->value * $this->retrieveValue($multiply));
    }

    /**
     * @param $divide
     *
     * @throws UnexpectedValueException
     * @return static
     */
    public function divide($divide)
    {
        if ($divide == 0)
        {
            throw new UnexpectedValueException('Division by zero is unacceptable');
        }

        return new static($this->value / $this->retrieveValue($divide));
    }

    /**
     * @param $by
     *
     * @return int
     */
    public function modulo($by)
    {
        return $this->value % $this->retrieveValue($by);
    }

    /**
     * @return number
     */
    public function value()
    {
        return $this->get();
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function set($value)
    {
        $this->value = $this->retrieveValue($value);

        return $this;
    }

    /**
     * @return number
     */
    public function get()
    {
        return $this->value;
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
     * @return int
     */
    public function length()
    {
        return Str::length((string) $this->value);
    }

    /**
     * @return bool
     */
    public function isTrue()
    {
        return $this->toBool()->isTrue();
    }

    /**
     * @return bool
     */
    public function isFalse()
    {
        return $this->toBool()->isFalse();
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isEquals($value)
    {
        return $this->value === $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function greaterThan($value)
    {
        return $this->value > $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function greaterThanOrEquals($value)
    {
        return $this->value >= $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function lowerThan($value)
    {
        return $this->value < $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function lowerThanOrEquals($value)
    {
        return $this->value <= $this->retrieveValue($value);
    }

    /**
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return bool((bool) $this->value);
    }

    /**
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return string((string) $this->value);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->value);
    }

    /**
     * @return number
     */
    public function __invoke()
    {
        return $this->get();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->value);
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
        $this->value = $this->retrieveValue(unserialize($serialized));
    }
}

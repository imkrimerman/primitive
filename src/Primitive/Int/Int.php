<?php namespace im\Primitive\Int;

use Serializable;
use UnexpectedValueException;

use im\Primitive\Bool\Bool;
use im\Primitive\Support\Str;


class Int implements Serializable {

    /**
     * @var int
     */
    protected $value;

    /**
     * @param     $value
     * @param int $default
     */
    function __construct($value, $default = 0)
    {
        $this->value = $this->getIntegerable($value, $default);
    }

    /**
     * @param $plus
     *
     * @return static
     */
    public function plus($plus)
    {
        return new static($this->value + $this->getIntegerable($plus));
    }

    /**
     * @param $minus
     *
     * @return static
     */
    public function minus($minus)
    {
        return new static($this->value - $this->getIntegerable($minus));
    }

    /**
     * @param $multiply
     *
     * @return static
     */
    public function multiply($multiply)
    {
        return new static($this->value * $this->getIntegerable($multiply));
    }

    /**
     * @param $divide
     *
     * @return static
     */
    public function divide($divide)
    {
        if ($divide == 0)
        {
            throw new UnexpectedValueException('Division by zero is unacceptable');
        }

        return new static($this->value / $this->getIntegerable($divide));
    }

    /**
     * @param $by
     *
     * @return int
     */
    public function modulo($by)
    {
        return $this->value % $this->getIntegerable($by);
    }

    /**
     * @return int
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
        $this->value = $this->getIntegerable($value);

        return $this;
    }

    /**
     * @return int
     */
    public function get()
    {
        return (int) $this->value;
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
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return bool($this->value);
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return float($this->value);
    }

    /**
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return string((string) $this->value);
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
        return $this->value === $this->getIntegerable($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function greaterThan($value)
    {
        return $this->value > $this->getIntegerable($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function greaterThanOrEquals($value)
    {
        return $this->value >= $this->getIntegerable($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function lowerThan($value)
    {
        return $this->value < $this->getIntegerable($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function lowerThanOrEquals($value)
    {
        return $this->value <= $this->getIntegerable($value);
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
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->value);
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
        $this->value = $this->getIntegerable(unserialize($serialized));
    }

    /**
     * @param     $value
     * @param int $default
     *
     * @return int
     */
    protected function getIntegerable($value, $default = 0)
    {
        switch (true)
        {
            case is_numeric($value):
            case is_bool($value):
                return (int) $value;
            case $value instanceof Int:
                return $value->value();
            case $value instanceof Float:
                return $value->toInt()->value();
            case $value instanceof Bool:
                return $value->toInt()->value();
            case $value instanceof String:
                return (int) $value->all();
            default:
                return $default;
        }
    }
}

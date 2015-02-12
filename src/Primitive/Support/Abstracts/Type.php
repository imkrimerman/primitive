<?php namespace im\Primitive\Support\Abstracts;

use InvalidArgumentException;
use Serializable;
use im\Primitive\Support\Dump\Dumper;
use im\Primitive\Support\Contracts\TypeContract;
use im\Primitive\Support\Traits\RetrievableTrait;

/**
 * Class Type
 *
 * @package im\Primitive\Support\Abstracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
abstract class Type implements TypeContract, Serializable {

    use RetrievableTrait;

    /**
     * Initialize inner value with given $value.
     *
     * @param mixed $value
     * @return $this
     */
    abstract protected function initialize($value);

    /**
     * Return inner value.
     *
     * @return mixed
     */
    abstract public function value();

    /**
     * Retrieve needed type from given $value.
     *
     * @param mixed $value
     * @return mixed
     */
    abstract protected function retrieveValue($value);

    /**
     * Magic method to auto convert Type to string.
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Return default Type value.
     *
     * @return mixed
     */
    abstract protected function getDefault();

    /**
     * Magic method.
     * Used to call methods without parameters as variables.
     * Throws InvalidArgumentException if method not exists.
     *
     * @param mixed $value
     * @return mixed
     */
    public function __get($value)
    {
        $value = $this->getStringable($value);

        if (method_exists($this, $value))
        {
            return $this->{$value}();
        }

        throw new InvalidArgumentException('Argument 1 is invalid, offset not exists.');
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of type.
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->value());
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the Type
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized The string representation of the object.
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->initialize(unserialize($serialized));
    }

    /**
     * Dump Type.
     * If $die is specified, it will end the script.
     *
     * @param bool $die
     */
    public function dump($die = false)
    {
        (new Dumper())->dump($this);

        if ($this->getBoolable($die)) die;
    }

    /**
     * Make new Type instance with given $value. Useful for chaining.
     *
     * @param mixed $value
     * @return static
     */
    public function make($value)
    {
        return new static($value);
    }

    /**
     * Statically create new Type Instance. Useful for chaining.
     *
     * @param mixed $value
     * @return static
     */
    public static function create($value)
    {
        return new static($value);
    }

    /**
     * Magic method to retrieve Type inner value.
     *
     * @return number
     */
    public function __invoke()
    {
        return $this->value();
    }
}

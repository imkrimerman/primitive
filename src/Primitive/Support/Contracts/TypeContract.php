<?php namespace im\Primitive\Support\Contracts;

use im\Primitive\Support\Dump\Dumper;
use InvalidArgumentException;


/**
 * Interface TypeContract
 *
 * @package im\Primitive\Support\Contracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface TypeContract {

    /**
     * Construct Type with given $value
     *
     * @param mixed $value
     */
    public function __construct($value);

    /**
     * Statically create new Type Instance. Useful for chaining.
     *
     * @param mixed $value
     * @return static
     */
    public static function create($value);

    /**
     * Return inner value.
     *
     * @return mixed
     */
    public function value();

    /**
     * Magic method to auto convert Type to string.
     *
     * @return string
     */
    public function __toString();

    /**
     * Magic method.
     * Used to call methods without parameters as variables.
     * Throws InvalidArgumentException if method not exists.
     *
     * @param mixed $value
     * @return mixed
     */
    public function __get($value);

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of type.
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize();

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the Type
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized The string representation of the object.
     * @return void
     */
    public function unserialize($serialized);

    /**
     * Dump Type.
     * If $die is specified, it will end the script.
     *
     * @param bool $die
     */
    public function dump($die = false);

    /**
     * Make new Type instance with given $value. Useful for chaining.
     *
     * @param mixed $value
     * @return static
     */
    public function make($value);

    /**
     * Magic method to retrieve Type inner value.
     *
     * @return number
     */
    public function __invoke();
}

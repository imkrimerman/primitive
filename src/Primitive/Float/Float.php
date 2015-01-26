<?php namespace im\Primitive\Float;

use Serializable;

class Float implements Serializable {

    /**
     * @var float
     */
    protected $value;

    /**
     * @param       $value
     * @param float $default
     */
    public function __construct($value, $default = 0.0)
    {
        $this->value = $this->getFloatable($value, $default);
    }

    /**
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return int((int) $this->value);
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
        $this->__construct(unserialize($serialized));
    }

    /**
     * @param       $value
     * @param float $default
     *
     * @return float
     */
    protected function getFloatable($value, $default = 0.0)
    {
        switch (true)
        {
            case is_numeric($value):
            case is_bool($value):
                return (float) $value;
            case $value instanceof Float:
                return $value->value();
            case $value instanceof Int:
                return $value->toFloat()->value();
            case $value instanceof Bool:
                return $value->toFloat()->value();
            case $value instanceof String:
                return (float) $value->all();
            default:
                return $default;
        }
    }
}

<?php namespace im\Primitive\Bool;


use im\Primitive\Int\Int;
use im\Primitive\String\String;
use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Contracts\TypeInterface;
use Serializable;

class Bool extends Type {

    /**
     * @var bool
     */
    protected $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->initialize($value);
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
     * @return mixed
     */
    public function value()
    {
        return (bool) $this->value;
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
     * @return bool
     */
    public function get()
    {
        return $this->value();
    }

    /**
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return int($this->value);
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
        return string($this->__toString());
    }

    /**
     * @return bool
     */
    public function isTrue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isFalse()
    {
        return ! $this->isTrue();
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
     * @return string
     */
    public function __toString()
    {
        return $this->value ? 'true' : 'false';
    }

    /**
     *
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
        $this->initialize(unserialize($serialized));
    }

    /**
     * @param $value
     */
    protected function initialize($value)
    {
        $this->value = $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function retrieveValue($value)
    {
        switch (true)
        {
            case is_bool($value):
                return $value;
            case is_numeric($value):
                return (bool) ((int) $value);
            case is_string($value):
                return $this->fromString($value);
            case $value instanceof Bool:
                return $value->value();
            case $value instanceof String:
                return $this->fromString($value->all());
            case $value instanceof Int:
                return $value->toBool()->value();
            case $value instanceof Float:
                return $value->toBool()->value();
            default:
                return $this->getDefault();
        }
    }

    /**
     * @param $value
     *
     * @return bool
     */
    protected function fromString($value)
    {
        $grammar = $this->getGrammar();

        if (isset($grammar[$value])) return $grammar[$value];

        return $this->getDefault();
    }

    /**
     * @return array
     */
    protected function getGrammar()
    {
        return [
            'true' => true, 'false' => false,
            'on' => true,   'off' => false,
            'yes' => true,  'no' => false,
            'y' => true,    'n' => false,
            '+' => true,    '-' => false
        ];
    }

    /**
     * @return bool
     */
    protected function getDefault()
    {
        return false;
    }
}

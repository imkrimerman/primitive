<?php namespace im\Primitive\Int;

use UnexpectedValueException;

use im\Primitive\Bool\Bool;
use im\Primitive\Support\Str;
use im\Primitive\Support\Abstracts\Number;


class Int extends Number {

    /**
     * @var int
     */
    protected $value;

    /**
     * @param $value
     */
    function __construct($value)
    {
        $this->initialize($value);
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return float($this->value);
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
     * @return int
     */
    protected function retrieveValue($value)
    {
        switch (true)
        {
            case is_numeric($value):
            case is_bool($value):
                return (int) $value;

            case $value instanceof Int:
            case $value instanceof String:
                return (int) $value->value();

            case $value instanceof Float:
            case $value instanceof Bool:
                return $value->toInt()->value();

            default:
                return $this->getDefault();
        }
    }

    /**
     * @return int
     */
    protected function getDefault()
    {
        return 0;
    }
}

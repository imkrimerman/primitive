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
     * @param     $value
     * @param int $default
     */
    function __construct($value, $default = 0)
    {
        $this->value = $this->retrieveValue($value, $default);
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
                return $value->value();
            case $value instanceof Float:
                return $value->toInt()->value();
            case $value instanceof Bool:
                return $value->toInt()->value();
            case $value instanceof String:
                return (int) $value->all();
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

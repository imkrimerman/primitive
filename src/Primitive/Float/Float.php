<?php namespace im\Primitive\Float;


use im\Primitive\Support\Abstracts\Number;

class Float extends Number {

    /**
     * @var float
     */
    protected $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $this->retrieveValue($value);
    }

    /**
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return int((int) $this->value);
    }

    /**
     * @param $value
     *
     * @return float
     */
    protected function retrieveValue($value)
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
                return $this->getDefault();
        }
    }

    /**
     * @return float
     */
    protected function getDefault()
    {
        return 0.00;
    }
}

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
        $this->initialize($value);
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
     */
    protected function initialize($value)
    {
        $this->value = $this->retrieveValue($value);
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
            case $value instanceof String:
                return (float) $value->value();

            case $value instanceof Int:
            case $value instanceof Bool:
                return $value->toFloat()->value();

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

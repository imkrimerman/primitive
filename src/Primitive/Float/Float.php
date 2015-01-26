<?php namespace im\Primitive\Float;

use im\Primitive\Support\Abstracts\Number;
use im\Primitive\Support\Traits\RetrievableTrait;
use im\Primitive\Support\Contracts\FloatInterface;


class Float extends Number implements FloatInterface{

    use RetrievableTrait;

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
     *
     * @return $this
     */
    protected function initialize($value)
    {
        $this->value = $this->retrieveValue($value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return float
     */
    protected function retrieveValue($value)
    {
        return $this->getFloatable($value, $this->getDefault());
    }

    /**
     * @return float
     */
    protected function getDefault()
    {
        return 0.00;
    }
}

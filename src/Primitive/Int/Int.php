<?php namespace im\Primitive\Int;

use im\Primitive\Support\Abstracts\Number;
use im\Primitive\Support\Traits\RetrievableTrait;
use im\Primitive\Support\Contracts\IntegerInterface;


class Int extends Number implements IntegerInterface {

    use RetrievableTrait;

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
     * @return int
     */
    protected function retrieveValue($value)
    {
        return $this->getIntegerable($value, $this->getDefault());
    }

    /**
     * @return int
     */
    protected function getDefault()
    {
        return 0;
    }
}

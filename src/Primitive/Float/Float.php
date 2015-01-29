<?php namespace im\Primitive\Float;

use im\Primitive\Support\Abstracts\Number;
use im\Primitive\Support\Contracts\FloatInterface;


class Float extends Number implements FloatInterface{

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
     * @return static
     */
    public function ceil()
    {
        return new static(ceil($this->value));
    }

    /**
     * @return static
     */
    public function floor()
    {
        return new static(floor($this->value));
    }

    /**
     * @param int $precision
     * @param int $mode
     *
     * @return static
     */
    public function round($precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        return new static(round($this->value, $precision, $mode));
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

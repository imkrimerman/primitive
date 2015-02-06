<?php namespace im\Primitive\Float;

use im\Primitive\Int\Int;
use im\Primitive\Support\Abstracts\Number;
use im\Primitive\Support\Contracts\FloatContract;


/**
 * Class Float
 *
 * @package im\Primitive\Float
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class Float extends Number implements FloatContract {

    /**
     * Storing value
     * @var float
     */
    protected $value;

    /**
     * Construct Float Type
     * Can be constructed with int, all real types, bool,
     * FloatContract, IntegerContract, StringContract, BooleanContract
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->initialize($value);
    }

    /**
     * Round fractions up
     *
     * @return static
     */
    public function ceil()
    {
        return new static(ceil($this->value));
    }

    /**
     * Round fractions down
     *
     * @return static
     */
    public function floor()
    {
        return new static(floor($this->value));
    }

    /**
     * Return the rounded Float Type to specified precision (number of digits after the decimal point).
     * precision can also be negative or zero (default).
     *
     * @param int $precision
     *      The optional number of decimal digits to round to.
     * @param int $mode
     *      One of PHP_ROUND_HALF_UP,
     *      PHP_ROUND_HALF_DOWN,
     *      PHP_ROUND_HALF_EVEN, or
     *      PHP_ROUND_HALF_ODD.
     *
     * @return static
     */
    public function round($precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        return new static(round($this->value, $precision, $mode));
    }

    /**
     * Convert Float Type to Int Type
     *
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return new Int((int) $this->value);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     * @return $this
     */
    protected function initialize($value)
    {
        $this->value = $this->retrieveValue($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     * @return float
     */
    protected function retrieveValue($value)
    {
        return $this->getFloatable($value, $this->getDefault());
    }

    /**
     * {@inheritdoc}
     *
     * @return float
     */
    protected function getDefault()
    {
        return 0.00;
    }
}

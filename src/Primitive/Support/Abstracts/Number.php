<?php namespace im\Primitive\Support\Abstracts;

use Serializable;
use UnexpectedValueException;

use im\Primitive\Int\Int;
use im\Primitive\Bool\Bool;
use im\Primitive\Float\Float;
use im\Primitive\String\String;
use im\Primitive\Container\Container;
use im\Primitive\Support\Contracts\TypeContract;
use im\Primitive\Support\Traits\RetrievableTrait;

/**
 * Class Number
 *
 * @package im\Primitive\Support\Abstracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
abstract class Number extends Type implements TypeContract, Serializable {

    use RetrievableTrait;

    /**
     * Storing value.
     * @var number
     */
    protected $value;

    /**
     * Construct Number Type.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->initialize($value);
    }

    /**
     * Setter for inner value.
     *
     * @param mixed $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = $this->retrieveValue($value);

        return $this;
    }

    /**
     * Getter for inner value.
     *
     * @return number
     */
    public function get()
    {
        return $this->value();
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Plus $value to inner value.
     *
     * @param mixed $value
     * @return static
     */
    public function plus($value)
    {
        return new static($this->value + $this->retrieveValue($value));
    }

    /**
     * Minus $value from inner value.
     *
     * @param mixed $value
     * @return static
     */
    public function minus($value)
    {
        return new static($this->value - $this->retrieveValue($value));
    }

    /**
     * Multiply inner value by $value.
     *
     * @param mixed $value
     * @return static
     */
    public function multiply($value)
    {
        return new static($this->value * $this->retrieveValue($value));
    }

    /**
     * Divide inner value by $value.
     *
     * @param mixed $value
     * @throws UnexpectedValueException
     * @return static
     */
    public function divide($value)
    {
        $retrieved = $this->retrieveValue($value);

        if ($retrieved === 0)
        {
            throw new UnexpectedValueException('Division by zero is unacceptable');
        }

        return new static($this->value / $retrieved);
    }

    /**
     * Calculate modulo.
     *
     * @param mixed $modulo
     * @return \im\Primitive\Int\Int
     */
    public function modulo($modulo)
    {
        return new Int($this->value % $this->retrieveValue($modulo));
    }

    /**
     * Raise inner value to the $power.
     *
     * @param $power
     * @return static
     */
    public function power($power)
    {
        return new static(pow($this->value, $this->retrieveValue($power)));
    }

    /**
     * Calculate square root.
     *
     * @return static
     */
    public function sqrt()
    {
        return new Float(sqrt($this->value));
    }

    /**
     * Return absolute value.
     *
     * @return static
     */
    public function abs()
    {
        return new static(abs($this->value));
    }

    /**
     * Sine.
     *
     * @return \im\Primitive\Float\Float
     */
    public function sin()
    {
        return new Float(sin($this->value));
    }

    /**
     * Cosine.
     *
     * @return \im\Primitive\Float\Float
     */
    public function cos()
    {
        return new Float(cos($this->value));
    }

    /**
     * Tangent.
     *
     * @return \im\Primitive\Float\Float
     */
    public function tan()
    {
        return new Float(tan($this->value));
    }

    /**
     * Return pi.
     *
     * @return \im\Primitive\Float\Float
     */
    public function pi()
    {
        return new Float(pi());
    }

    /**
     * Calculate factorial of inner value.
     *
     * @return int|number
     */
    public function factorial()
    {
        if ($this->value < 100)
        {
            return factorial_recursive($this->value);
        }

        return factorial($this->value);
    }

    /**
     * Format a number with grouped thousands and return String.
     *
     * @param int|IntegerContract $decimals
     * @param string|StringContract $decimalDelimiter
     * @param string|StringContract $thousandDelimiter
     *
     * @return \im\Primitive\Float\Float
     */
    public function format($decimals = 2, $decimalDelimiter = '.', $thousandDelimiter = ' ')
    {
        return new String(
            number_format(
                (float) $this->value,
                $this->getIntegerable($decimals),
                $this->getStringable($decimalDelimiter),
                $this->getStringable($thousandDelimiter)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function length()
    {
        return (new String($this->value))->length();
    }

    /**
     * Check if is inner value is true.
     *
     * @return bool
     */
    public function isTrue()
    {
        return $this->toBool()->isTrue();
    }

    /**
     * Check if is inner value is false.
     * @return bool
     */
    public function isFalse()
    {
        return $this->toBool()->isFalse();
    }

    /**
     * Check if is inner value is equals to $value.
     *
     * @param mixed $value
     * @return bool
     */
    public function isEqual($value)
    {
        return $this->value === $this->retrieveValue($value);
    }

    /**
     * Check if is inner value is negative.
     *
     * @return bool
     */
    public function isNegative()
    {
        return $this->value < 0;
    }

    /**
     * Check if is inner value is not negative.
     *
     * @return bool
     */
    public function isNotNegative()
    {
        return ! $this->isNegative();
    }

    /**
     * Check if is inner value is greater than $value.
     *
     * @param mixed $value
     * @return bool
     */
    public function isGreaterThan($value)
    {
        return $this->value > $this->retrieveValue($value);
    }

    /**
     * Check if is inner value is greater than or equal to $value.
     *
     * @param mixed $value
     * @return bool
     */
    public function isGreaterThanOrEqual($value)
    {
        return $this->value >= $this->retrieveValue($value);
    }

    /**
     * Check if is inner value is lower than $value.
     *
     * @param mixed $value
     * @return bool
     */
    public function isLowerThan($value)
    {
        return $this->value < $this->retrieveValue($value);
    }

    /**
     * Check if is inner value is lower than or equal to $value.
     *
     * @param mixed $value
     * @return bool
     */
    public function isLowerThanOrEqual($value)
    {
        return $this->value <= $this->retrieveValue($value);
    }

    /**
     * Convert Number Type to Bool Type.
     *
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return new Bool((bool) $this->value);
    }

    /**
     * Convert Number Type to String Type.
     *
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return new String((string) $this->value);
    }

    /**
     * Convert Number Type to Container Type.
     *
     * @return \im\Primitive\Container\Container
     */
    public function toContainer()
    {
        return new Container([$this->value]);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}

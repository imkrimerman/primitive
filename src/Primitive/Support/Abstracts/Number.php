<?php namespace im\Primitive\Support\Abstracts;

use im\Primitive\Support\Str;
use Serializable;
use im\Primitive\Support\Contracts\TypeInterface;
use im\Primitive\Support\Traits\RetrievableTrait;


abstract class Number extends Type implements TypeInterface, Serializable {

    use RetrievableTrait;

    /**
     * @var number
     */
    protected $value;

    /**
     * @param $value
     */
    abstract public function __construct($value);

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
     * @return number
     */
    public function get()
    {
        return $this->value();
    }

    /**
     * @return number
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @param $plus
     *
     * @return static
     */
    public function plus($plus)
    {
        return new static($this->value + $this->retrieveValue($plus));
    }

    /**
     * @param $minus
     *
     * @return static
     */
    public function minus($minus)
    {
        return new static($this->value - $this->retrieveValue($minus));
    }

    /**
     * @param $multiply
     *
     * @return static
     */
    public function multiply($multiply)
    {
        return new static($this->value * $this->retrieveValue($multiply));
    }

    /**
     * @param $divide
     *
     * @throws UnexpectedValueException
     * @return static
     */
    public function divide($divide)
    {
        $retrieved = $this->retrieveValue($divide);

        if ($retrieved === 0)
        {
            throw new UnexpectedValueException('Division by zero is unacceptable');
        }

        return new static($this->value / $retrieved);
    }

    /**
     * @param $by
     *
     * @return \im\Primitive\Int\Int
     */
    public function modulo($by)
    {
        return int($this->value % $this->retrieveValue($by));
    }

    /**
     * @param $pow
     *
     * @return static
     */
    public function power($pow)
    {
        return new static(pow($this->value, $this->retrieveValue($pow)));
    }

    /**
     * @return static
     */
    public function sqrt()
    {
        return float(sqrt($this->value));
    }

    /**
     * @return static
     */
    public function abs()
    {
        return new static(abs($this->value));
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function sin()
    {
        return float(sin($this->value));
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function cos()
    {
        return float(cos($this->value));
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function tan()
    {
        return float(tan($this->value));
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function pi()
    {
        return float(pi());
    }

    /**
     * @param bool $recursive
     *
     * @return int|number
     */
    public function factorial($recursive = true)
    {
        if ($recursive)
        {
            return factorialRecursive($this->value);
        }

        return factorial($this->value);
    }

    /**
     * @param int    $decimals
     * @param string $decimalDelimiter
     * @param string $thousandDelimiter
     *
     * @return \im\Primitive\Float\Float
     */
    public function format($decimals = 2, $decimalDelimiter = '.', $thousandDelimiter = ' ')
    {
        return string(
            number_format(
                (float) $this->value,
                (int) $decimals,
                (string) $decimalDelimiter,
                (string) $thousandDelimiter
            )
        );
    }

    /**
     * @return int
     */
    public function length()
    {
        return Str::length((string) $this->value);
    }

    /**
     * @return bool
     */
    public function isTrue()
    {
        return $this->toBool()->isTrue();
    }

    /**
     * @return bool
     */
    public function isFalse()
    {
        return $this->toBool()->isFalse();
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isEqual($value)
    {
        return $this->value === $this->retrieveValue($value);
    }

    /**
     * @return bool
     */
    public function isNegative()
    {
        return $this->value < 0;
    }

    /**
     * @return bool
     */
    public function isNotNegative()
    {
        return ! $this->isNegative();
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isGreaterThan($value)
    {
        return $this->value > $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isGreaterThanOrEqual($value)
    {
        return $this->value >= $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isLowerThan($value)
    {
        return $this->value < $this->retrieveValue($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isLowerThanOrEqual($value)
    {
        return $this->value <= $this->retrieveValue($value);
    }

    /**
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return bool((bool) $this->value);
    }

    /**
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return string((string) $this->value);
    }

    /**
     * @return \im\Primitive\Container\Container
     */
    public function toContainer()
    {
        return container([$this->value]);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}

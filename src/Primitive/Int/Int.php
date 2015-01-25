<?php namespace im\Primitive\Int;

use im\Primitive\Bool\Bool;
use im\Primitive\Support\Str;
use UnexpectedValueException;


class Int {

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
        $this->value = $this->getIntegerable($value, $default);
    }

    /**
     * @param $plus
     *
     * @return static
     */
    public function plus($plus)
    {
        return new static($this->value + $this->getIntegerable($plus));
    }

    /**
     * @param $minus
     *
     * @return static
     */
    public function minus($minus)
    {
        return new static($this->value - $this->getIntegerable($minus));
    }

    /**
     * @param $multiply
     *
     * @return static
     */
    public function multiply($multiply)
    {
        return new static($this->value * $this->getIntegerable($multiply));
    }

    /**
     * @param $divide
     *
     * @return static
     */
    public function divide($divide)
    {
        if ($divide == 0)
        {
            throw new UnexpectedValueException('Division by zero is unacceptable');
        }

        return new static($this->value / $this->getIntegerable($divide));
    }

    /**
     * @return int
     */
    public function value()
    {
        return $this->get();
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function set($value)
    {
        $this->value = $this->getIntegerable($value);

        return $this;
    }

    /**
     * @return int
     */
    public function get()
    {
        return (int) $this->value;
    }

    /**
     * @param $value
     *
     * @return static
     */
    public function make($value)
    {
        return new static($value);
    }

    /**
     * @return int
     */
    public function length()
    {
        return Str::length((string) $this->value);
    }

    /**
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return bool($this->value);
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
     * @return mixed
     */
    public function __get($value)
    {
        if (method_exists($this, $value))
        {
            return $this->{$value}();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->value);
    }

    /**
     * @param     $value
     * @param int $default
     *
     * @return int
     */
    protected function getIntegerable($value, $default = 0)
    {
        if ($value instanceof Bool)
        {
            $value = $value->toInt();
        }

        return (int) (is_numeric($value)) ? $value : $default;
    }
}

<?php namespace im\Primitive\Int;

use im\Primitive\Bool\Bool;
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
        $this->value = $this->getInteger($value, $default);
    }

    /**
     * @param $plus
     *
     * @return static
     */
    public function plus($plus)
    {
        return new static($this->value + $this->getInteger($plus));
    }

    /**
     * @param $minus
     *
     * @return static
     */
    public function minus($minus)
    {
        return new static($this->value - $this->getInteger($minus));
    }

    /**
     * @param $multiply
     *
     * @return static
     */
    public function multiply($multiply)
    {
        return new static($this->value * $this->getInteger($multiply));
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

        return new static($this->value / $this->getInteger($divide));
    }

    /**
     * @return int
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function set($value)
    {
        $this->value = $this->getInteger($value);

        return $value;
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
        return strlen((string) $this->value);
    }

    public function toBool()
    {
        return new Bool($this->value);
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
            return $value();
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
    protected function getInteger($value, $default = 0)
    {
        return is_numeric($value) ? (int) $value : $default;
    }
}

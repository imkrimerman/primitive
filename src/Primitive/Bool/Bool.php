<?php namespace im\Primitive\Bool;

use im\Primitive\Int\Int;

class Bool {

    /**
     * @var bool
     */
    protected $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $this->getBool($value);
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
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function set($value)
    {
        $this->value = $this->getBool($value);

        return $this;
    }

    /**
     * @return int
     */
    public function toInt()
    {
        return new Int((int) $this->value);
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
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->value);
    }

    /**
     * @param      $value
     * @param bool $default
     *
     * @return bool
     */
    protected function getBool($value, $default = false)
    {
        if (is_bool($value))
        {
            return $value;
        }
        elseif (is_numeric($value))
        {
            return (bool) ((int) $value);
        }

        return $default;
    }
}

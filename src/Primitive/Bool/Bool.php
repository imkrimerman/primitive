<?php namespace im\Primitive\Bool;

use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Traits\RetrievableTrait;
use im\Primitive\Support\Contracts\BooleanInterface;


class Bool extends Type implements BooleanInterface{

    use RetrievableTrait;

    /**
     * @var bool
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
     * @return mixed
     */
    public function value()
    {
        return (bool) $this->value;
    }

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
     * @return bool
     */
    public function get()
    {
        return $this->value();
    }

    /**
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return int($this->value);
    }

    /**
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return float($this->value);
    }

    /**
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return string($this->__toString());
    }

    /**
     * @return \im\Primitive\Container\Container
     */
    public function toContainer()
    {
        return container([$this->value]);
    }

    /**
     * @return bool
     */
    public function isTrue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isFalse()
    {
        return ! $this->isTrue();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value ? 'true' : 'false';
    }

    /**
     *
     */
    public function __destruct()
    {
        unset($this->value);
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
     * @return bool
     */
    protected function retrieveValue($value)
    {
        return $this->getBoolable($value, $this->getDefault());
    }

    /**
     * @return bool
     */
    protected function getDefault()
    {
        return false;
    }
}

<?php namespace im\Primitive\Bool;

use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Traits\RetrievableTrait;
use im\Primitive\Support\Contracts\BooleanInterface;
use im\Primitive\Support\Contracts\FloatInterface;
use im\Primitive\Support\Contracts\IntegerInterface;
use im\Primitive\Support\Contracts\StringInterface;


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
        return a([$this->value]);
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
     * @param $value
     *
     * @return bool
     */
    protected function fromString($value)
    {
        $grammar = $this->getGrammar();

        $value = $this->getStringable($value);

        if (isset($grammar[$value])) return $grammar[$value];

        return $this->getDefault();
    }

    /**
     * @return array
     */
    protected function getGrammar()
    {
        return [
            'true' => true, 'false' => false,
            'on' => true,   'off' => false,
            'yes' => true,  'no' => false,
            'y' => true,    'n' => false,
            '+' => true,    '-' => false
        ];
    }

    /**
     * @return bool
     */
    protected function getDefault()
    {
        return false;
    }
}

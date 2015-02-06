<?php namespace im\Primitive\Bool;

use im\Primitive\Int\Int;
use im\Primitive\Float\Float;
use im\Primitive\String\String;
use im\Primitive\Container\Container;
use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Traits\RetrievableTrait;
use im\Primitive\Support\Contracts\BooleanContract;

/**
 * Class Bool
 *
 * @package im\Primitive\Bool
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class Bool extends Type implements BooleanContract {

    use RetrievableTrait;

    /**
     * Storing value
     *
     * @var bool
     */
    protected $value;

    /**
     * Construct Bool Type
     *
     * Can be constructed with bool, string or StringContract.
     * Supported string map:
     *   'true' => true, 'false' => false,
     *   'on' => true,   'off' => false,
     *   'yes' => true,  'no' => false,
     *   'y' => true,    'n' => false,
     *   '+' => true,    '-' => false
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->initialize($value);
    }

    /**
     * Magic get method to support method calls (without parameters) as variables
     *
     * @param string|StringContract $value
     *
     * @return mixed
     */
    public function __get($value)
    {
        $value = $this->getStringable($value);

        if (method_exists($this, $value))
        {
            return $this->{$value}();
        }

        return $this->getDefault();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function value()
    {
        return (bool) $this->value;
    }

    /**
     * Setter for inner value
     *
     * @param bool|string|StringContract|BooleanContract $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = $this->retrieveValue($value);

        return $this;
    }

    /**
     * Getter for inner value (alias for value())
     *
     * @return bool
     */
    public function get()
    {
        return $this->value();
    }

    /**
     * Convert Bool Type to Int Type
     *
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return new Int($this->value);
    }

    /**
     * Convert Bool Type to Float Type
     *
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return new Float($this->value);
    }

    /**
     * Convert Bool Type to String Type
     * Conversion occurs correctly. If you have `false` it will convert to `false` string
     *
     * @return \im\Primitive\String\String
     */
    public function toString()
    {
        return new String($this->__toString());
    }

    /**
     * Convert Bool Type to Container Type
     * Value will be at zero index
     *
     * @return \im\Primitive\Container\Container
     */
    public function toContainer()
    {
        return new Container([$this->value]);
    }

    /**
     * Helper method
     * Return true if value is true
     *
     * @return bool
     */
    public function isTrue()
    {
        return $this->value;
    }

    /**
     * Helper method
     * Return false if value is false
     *
     * @return bool
     */
    public function isFalse()
    {
        return ! $this->isTrue();
    }

    /**
     * Magic string method to support right string conversion
     * Return string `true` if value is true, otherwise `false`
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value ? 'true' : 'false';
    }

    /**
     * Destructor
     *
     * Unset inner value
     */
    public function __destruct()
    {
        unset($this->value);
    }

    /**
     * {@inheritdoc}
     *
     * @param bool|string|BooleanContract|StringContract $value
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
     * @param $value
     * @return bool
     */
    protected function retrieveValue($value)
    {
        return $this->getBoolable($value, $this->getDefault());
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function getDefault()
    {
        return false;
    }
}

<?php namespace im\Primitive\Support\Traits;

use Closure;
use im\Primitive\Bool\Bool;
use im\Primitive\Support\Abstracts\Number;
use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Contracts\ArrayableContract;
use im\Primitive\Support\Contracts\BooleanContract;
use im\Primitive\Support\Contracts\ContainerContract;
use im\Primitive\Support\Contracts\FloatContract;
use im\Primitive\Support\Contracts\IntegerContract;
use im\Primitive\Support\Contracts\StringContract;

/**
 * Class RetrievableTrait
 *
 * @package im\Primitive\Support\Traits
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
trait RetrievableTrait {

    /**
     * Retrieve array from array, ContainerContract, ArrayableContract or
     * object, otherwise return $default.
     *
     * @param mixed $value
     * @param mixed $default
     * @return mixed
     */
    public function getArrayable($value, $default = null)
    {
        switch (true)
        {
            case is_array($value);
                return $value;
            case $value instanceof ContainerContract:
                return $value->value();
            case $value instanceof ArrayableContract:
                return $value->toArray();
            case is_object($value):
                return get_object_vars($value);
            default:
                return value($default);
        }
    }

    /**
     * Retrieve string from number, string, bool, array,  FloatContract, IntegerContract
     * StringContract, BooleanContract, ContainerContract, ArrayableContract or
     * object that has __toString method, otherwise return $default.
     *
     * @param mixed $value
     * @param mixed $default
     * @return string
     */
    public function getStringable($value, $default = null)
    {
        switch (true)
        {
            case is_string($value):
            case $value instanceof StringContract:
            case $value instanceof BooleanContract:
            case $value instanceof IntegerContract:
            case $value instanceof FloatContract:
                return (string) $value;

            case is_array($value):
            case $value instanceof ContainerContract:
            case $value instanceof ArrayableContract:
                return (string) container($value)->join();

            case is_bool($value):
                return (string) bool($value);

            case is_numeric($value):
            case is_object($value) && method_exists($value, '__toString'):
                return (string) $value;

            default:
                return value($default);
        }
    }

    /**
     * Retrieve bool from number, string, bool, FloatContract, IntegerContract
     * StringContract, BooleanContract, ContainerContract, otherwise return $default.
     *
     * @param mixed $value
     * @param mixed $default
     * @return bool
     */
    public function getBoolable($value, $default = null)
    {
        switch (true)
        {
            case is_bool($value):
                return $value;

            case is_numeric($value):
                return (bool) ((int) $value);

            case is_string($value):
            case $value instanceof StringContract:
                return $this->boolFromString($value);

            case $value instanceof BooleanContract:
                return $value->value();

            case $value instanceof IntegerContract:
            case $value instanceof FloatContract:
                return $value->toBool()->value();

            case $value instanceof ContainerContract:
                return $value->isNotEmpty();

            default:
                return value($default);
        }
    }

    /**
     * Retrieve int from number, string, bool, FloatContract, IntegerContract
     * StringContract, BooleanContract, otherwise return $default.
     *
     * @param mixed $value
     * @param mixed $default
     * @return int
     */
    public function getIntegerable($value, $default = null)
    {
        switch (true)
        {
            case is_numeric($value):
            case is_string($value):
            case is_bool($value):
                return (int) $value;

            case $value instanceof IntegerContract:
            case $value instanceof StringContract:
                return (int) $value->value();

            case $value instanceof FloatContract:
            case $value instanceof BooleanContract:
                return $value->toInt()->value();

            default:
                return value($default);
        }
    }

    /**
     * Retrieve float from number, string, bool, FloatContract, IntegerContract
     * StringContract, BooleanContract, otherwise return $default.
     *
     * @param mixed $value
     * @param mixed $default
     * @return float|mixed
     */
    public function getFloatable($value, $default = null)
    {
        switch (true)
        {
            case is_numeric($value):
            case is_string($value):
            case is_bool($value):
                return (float) $value;

            case $value instanceof FloatContract:
            case $value instanceof StringContract:
                return (float) $value->value();

            case $value instanceof IntegerContract:
            case $value instanceof BooleanContract:
                return $value->toFloat()->value();

            default:
                return value($default);
        }
    }

    /**
     * Retrieve number from any type or return default.
     *
     * @param $value
     * @param null|mixed $default
     * @return float|int|mixed
     */
    public function getNumberable($value, $default = null)
    {
        switch(true)
        {
            case is_int($value):
            case is_float($value):
                return $value;
            case is_numeric($value) && strpos($value, '.'):
                return (float) $value;
            case is_numeric($value):
            case is_bool($value):
                return (int) $value;
            case $value instanceof IntegerContract:
            case $value instanceof FloatContract:
                return $value->value();
            case $value instanceof BooleanContract:
                return $value->toInt()->value();
            case $value instanceof StringContract:
                return (int) $value->value();
            default:
                return value($default);
        }
    }

    /**
     * Retrieve value that can be used in search methods.
     *
     * @param mixed $value
     * @param mixed $default
     * @return array|string|\Closure|mixed
     */
    public function getSearchable($value, $default = [])
    {
        if ($this->isArrayable($value))
        {
            return $this->getArrayable($value, $default);
        }
        elseif ($this->isStringable($value))
        {
            return $this->getStringable($value, $default);
        }
        elseif ($value instanceof Closure)
        {
            return $value;
        }

        return value($default);
    }

    /**
     * Check if $value is Arrayable.
     *
     * @param mixed $value
     * @return bool
     */
    public function isArrayable($value)
    {
        return  is_array($value) ||
                $value instanceof ContainerContract ||
                $value instanceof ArrayableContract ||
                is_object($value) && ! $this->isStringable($value) && ! $this->isIntegerable($value);
    }

    /**
     * Check if $value is Stringable.
     * Second argument can be specified to check strict.
     *
     * @param mixed $value
     * @param bool  $strict
     * @return bool
     */
    public function isStringable($value, $strict = false)
    {
        if ( ! $this->getBoolable($strict))
        {
            return  is_string($value) ||
                    is_array($value) ||
                    is_numeric($value) ||
                    is_bool($value) ||
                    $value instanceof StringContract ||
                    $value instanceof ContainerContract ||
                    $value instanceof ArrayableContract ||
                    (is_object($value) && method_exists($value, '__toString'));
        }

        return is_string($value) || $strict instanceof StringContract;
    }

    /**
     * Check if $value is Integerable.
     * Second argument can be specified to check strict.
     *
     * @param mixed $value
     * @param bool  $strict
     * @return bool
     */
    public function isIntegerable($value, $strict = false)
    {
        if ( ! $this->getBoolable($strict))
        {
            return  is_numeric($value) ||
                    is_string($value) ||
                    is_bool($value) ||
                    $value instanceof IntegerContract ||
                    $value instanceof FloatContract ||
                    $value instanceof StringContract ||
                    $value instanceof BooleanContract;
        }

        return  is_int($value) ||
                is_real($value) ||
                $value instanceof IntegerContract ||
                $value instanceof FloatContract;
    }

    /**
     * Check if $value is Floatable.
     * Second argument can be specified to check strict.
     *
     * @param mixed $value
     * @param bool  $strict
     * @return bool
     */
    public function isFloatable($value, $strict = false)
    {
        return $this->isIntegerable($value, $strict);
    }

    /**
     * Check if $value is Boolable.
     * Second argument can be specified to check strict.
     *
     * @param mixed $value
     * @param bool  $strict
     * @return bool
     */
    public function isBoolable($value, $strict = false)
    {
        if ( ! $this->getBoolable($strict))
        {
            return $this->isIntegerable($value);
        }

        return is_bool($value) || $value instanceof BooleanContract;
    }

    /**
     * Construct proper Bool from string using grammar.
     *
     * @param mixed $value
     * @return bool
     */
    protected function boolFromString($value)
    {
        $grammar = $this->getGrammar();

        $value = $this->getStringable($value);

        if (isset($grammar[$value])) return $grammar[$value];

        return false;
    }

    /**
     * Return grammar to construct Bool from string.
     *
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
     * Check if $value is instance of Primitive Type.
     *
     * @param mixed $value
     * @return bool
     */
    public function isType($value)
    {
        return $value instanceof Type;
    }

    /**
     * Check if $value is not instance of Primitive Type.
     *
     * @param mixed $value
     * @return bool
     */
    public function isNotType($value)
    {
        return ! $this->isType($value);
    }
}

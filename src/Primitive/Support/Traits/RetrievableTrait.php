<?php namespace im\Primitive\Support\Traits;

use stdClass;
use im\Primitive\Support\Contracts\ArrayableInterface;
use im\Primitive\Support\Contracts\BooleanInterface;
use im\Primitive\Support\Contracts\ContainerInterface;
use im\Primitive\Support\Contracts\FloatInterface;
use im\Primitive\Support\Contracts\IntegerInterface;
use im\Primitive\Support\Contracts\StringInterface;


trait RetrievableTrait {

    /**
     * @param      $value
     * @param null $default
     *
     * @return mixed
     */
    public function getArrayable($value, $default = null)
    {
        switch (true)
        {
            case is_array($value);
                return $value;
            case $value instanceof ContainerInterface:
                return $value->value();
            case $value instanceof ArrayableInterface:
                return $value->toArray();
            case $value instanceof stdClass:
                return object_public_var($value);
            default:
                return $default;
        }
    }

    /**
     * @param      $value
     * @param null $default
     *
     * @return string
     */
    public function getStringable($value, $default = null)
    {
        switch (true)
        {
            case is_string($value):
                return $value;

            case $value instanceof StringInterface:
                return $value->value();

            case is_array($value):
            case $value instanceof ContainerInterface:
            case $value instanceof ArrayableInterface:
                return (string) container($this->getArrayable($value))->implode();

            case is_object($value) && method_exists($value, '__toString'):
                return (string) $value;

            default:
                return $default;
        }
    }

    /**
     * @param      $value
     * @param null $default
     *
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
            case $value instanceof StringInterface:
                return $this->fromString($value);

            case $value instanceof BooleanInterface:
                return $value->value();

            case $value instanceof IntegerInterface:
            case $value instanceof FloatInterface:
                return $value->toBool()->value();

            default:
                return $default;
        }
    }

    /**
     * @param      $value
     * @param null $default
     *
     * @return int
     */
    public function getIntegerable($value, $default = null)
    {
        switch (true)
        {
            case is_int($value):
            case is_numeric($value):
            case is_bool($value):
                return (int) $value;

            case $value instanceof IntegerInterface:
            case $value instanceof StringInterface:
                return (int) $value->value();

            case $value instanceof FloatInterface:
            case $value instanceof BooleanInterface:
                return $value->toInt()->value();

            default:
                return $default;
        }
    }

    /**
     * @param      $value
     * @param null $default
     *
     * @return float
     */
    public function getFloatable($value, $default = null)
    {
        switch (true)
        {
            case is_numeric($value):
            case is_bool($value):
                return (float) $value;

            case $value instanceof FloatInterface:
            case $value instanceof StringInterface:
                return (float) $value->value();

            case $value instanceof IntegerInterface:
            case $value instanceof BooleanInterface:
                return $value->toFloat()->value();

            default:
                return $default;
        }
    }

    /**
     * @param      $value
     * @param null $default
     *
     * @return array|string
     */
    public function getSearchable($value, $default = null)
    {
        if ($this->isStringable($value))
        {
            return $this->getStringable($value, $default);
        }

        return $this->getArrayable($value, $default);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isArrayable($value)
    {
        return  is_array($value) ||
                $value instanceof ContainerInterface ||
                $value instanceof ArrayableInterface ||
                $value instanceof stdClass;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isStringable($value)
    {
        return  is_string($value) ||
                is_array($value) ||
                $value instanceof StringInterface ||
                $value instanceof ContainerInterface ||
                $value instanceof ArrayableInterface ||
                (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isIntegerable($value)
    {
        return  is_numeric($value) ||
                is_bool($value) ||
                $value instanceof IntegerInterface ||
                $value instanceof FloatInterface ||
                $value instanceof StringInterface ||
                $value instanceof BooleanInterface;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isFloatable($value)
    {
        return $this->isIntegerable($value);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function isBoolable($value)
    {
        return  is_bool($value) ||
                is_numeric($value) ||
                is_string($value) ||
                $value instanceof StringInterface ||
                $value instanceof BooleanInterface ||
                $value instanceof IntegerInterface ||
                $value instanceof FloatInterface;
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
}

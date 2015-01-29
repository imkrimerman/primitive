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
     * @param array|ContainerInterface|ArrayableInterface|stdClass $value
     * @param null|array $default
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
                return get_object_vars($value);
            default:
                return $default;
        }
    }

    /**
     * @param string|StringInterface|array|ContainerInterface|ArrayableInterface|bool|number|stdClass $value
     * @param null|string $default
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
                return (string) container($value)->join();

            case is_bool($value):
                return (string) bool($value);

            case is_numeric($value):
            case is_object($value) && method_exists($value, '__toString'):
                return (string) $value;

            default:
                return $default;
        }
    }

    /**
     * @param bool|number|StringInterface|BooleanInterface|integerInterface|FloatInterface $value
     * @param null|bool $default
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
     * @param number|string|bool|IntegerInterface|StringInterface|FloatInterface|BooleanInterface $value
     * @param null|int $default
     *
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
     * @param number|bool|FloatInterface|StringInterface|IntegerInterface|BooleanInterface $value
     * @param null|float $default
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
     * @param string|StringInterface|array|ContainerInterface|ArrayableInterface|bool|number|stdClass $value
     * @param null|string|array $default
     *
     * @return array|string
     */
    public function getSearchable($value, $default = null)
    {
        if ($this->isArrayable($value))
        {
            return $this->getArrayable($value, $default);
        }

        return $this->getStringable($value, $default);
    }

    /**
     * @param array|ContainerInterface|ArrayableInterface|stdClass $value
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
     * @param string|array|number|bool|StringInterface|ContainerInterface|ArrayableInterface|stdClass $value
     *
     * @return bool
     */
    public function isStringable($value)
    {
        return  is_string($value) ||
                is_array($value) ||
                is_numeric($value) ||
                is_bool($value) ||
                $value instanceof StringInterface ||
                $value instanceof ContainerInterface ||
                $value instanceof ArrayableInterface ||
                (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * @param number|string|bool|IntegerInterface|FloatInterface|StringInterface|BooleanInterface $value
     *
     * @return bool
     */
    public function isIntegerable($value)
    {
        return  is_numeric($value) ||
                is_string($value) ||
                is_bool($value) ||
                $value instanceof IntegerInterface ||
                $value instanceof FloatInterface ||
                $value instanceof StringInterface ||
                $value instanceof BooleanInterface;
    }

    /**
     * @param number|string|bool|IntegerInterface|FloatInterface|StringInterface|BooleanInterface $value
     *
     * @return bool
     */
    public function isFloatable($value)
    {
        return $this->isIntegerable($value);
    }

    /**
     * @param bool|number|string|IntegerInterface|FloatInterface|StringInterface|BooleanInterface $value
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
     * @param string|StringInterface|array|ContainerInterface|ArrayableInterface|bool|int|float|stdClass $value
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

<?php namespace im\Primitive\Support\Traits;

use im\Primitive\Support\Contracts\ArrayableContract;
use im\Primitive\Support\Contracts\BooleanContract;
use im\Primitive\Support\Contracts\ContainerContract;
use im\Primitive\Support\Contracts\FloatContract;
use im\Primitive\Support\Contracts\IntegerContract;
use im\Primitive\Support\Contracts\StringContract;


trait RetrievableTrait {

    /**
     * @param mixed $value
     * @param mixed $default
     *
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
     * @param mixed $value
     * @param mixed $default
     *
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
     * @param mixed $value
     * @param mixed $default
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
            case $value instanceof StringContract:
                return $this->fromString($value);

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
     * @param mixed $value
     * @param mixed $default
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
     * @param mixed $value
     * @param mixed $default
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
     * @param mixed $value
     * @param mixed $default
     *
     * @return array|string
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
        elseif (is_callable($value))
        {
            return $value;
        }

        return value($default);
    }

    /**
     * @param mixed $value
     *
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
     * @param mixed $value
     *
     * @param bool  $strict
     *
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
     * @param mixed $value
     *
     * @param bool  $strict
     *
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
     * @param mixed $value
     *
     * @param bool  $strict
     *
     * @return bool
     */
    public function isFloatable($value, $strict = false)
    {
        return $this->isIntegerable($value, $strict);
    }

    /**
     * @param mixed $value
     *
     * @param bool  $strict
     *
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
     * @param mixed $value
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
     * @return string
     */
    protected function getDefault()
    {
        return '';
    }
}

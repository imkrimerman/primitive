<?php namespace im\Primitive\Int;

use im\Primitive\Float\Float;
use im\Primitive\Support\Abstracts\Number;
use im\Primitive\Support\Contracts\IntegerContract;

/**
 * Class Int
 *
 * @package im\Primitive\Int
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class Int extends Number implements IntegerContract {

    /**
     * Storing value
     * @var int
     */
    protected $value;

    /**
     * Convert Int Type to Float Type
     *
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return new Float($this->value);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
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
     * @param mixed $value
     * @return int
     */
    protected function retrieveValue($value)
    {
        return $this->getIntegerable($value, $this->getDefault());
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    protected function getDefault()
    {
        return 0;
    }
}

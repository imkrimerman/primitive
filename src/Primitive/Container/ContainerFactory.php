<?php namespace im\Primitive\Container;


use SebastianBergmann\RecursionContext\InvalidArgumentException;

class ContainerFactory {

    const SIMPLE = 1;
    const REVERTABLE = 2;

    /**
     * @param mixed $from
     * @param int   $type
     *
     * @return \im\Primitive\Container\Container|\im\Primitive\Container\RevertableContainer
     */
    public function make($from, $type)
    {
        switch ($type)
        {
            case static::SIMPLE:
                return new Container($from);
            case static::REVERTABLE:
                return new RevertableContainer($from);
            default:
                throw new InvalidArgumentException('Argument 2 is invalid');
        }
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static;
    }
}

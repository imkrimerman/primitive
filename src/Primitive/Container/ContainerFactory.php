<?php namespace im\Primitive\Container;

use \InvalidArgumentException;

/**
 * Class ContainerFactory
 *
 * @package im\Primitive\Container
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class ContainerFactory {

    /**
     * Simple Container Type
     * @var int
     */
    const SIMPLE = 1;

    /**
     * Revertable Container Type
     * @var int
     */
    const REVERTABLE = 2;

    /**
     * Return constructed instance of Container Type
     *
     * @param mixed $from
     * @param int   $type
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
     * Create Container Factory Instance
     *
     * @return static
     */
    public static function create()
    {
        return new static;
    }
}

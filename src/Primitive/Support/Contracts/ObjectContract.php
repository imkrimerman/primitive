<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface ObjectContract
 *
 * @package im\Primitive\Support\Contracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface ObjectContract extends TypeContract{

    /**
     * @return mixed
     */
    public function toString();

    /**
     * @return mixed
     */
    public function toContainer();
}

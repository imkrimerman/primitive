<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface FloatContract
 *
 * @package im\Primitive\Support\Contracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface FloatContract extends TypeContract {

    /**
     * @return mixed
     */
    public function toInt();

    /**
     * @return mixed
     */
    public function toBool();

    /**
     * @return mixed
     */
    public function toString();

    /**
     * @return mixed
     */
    public function toContainer();
}

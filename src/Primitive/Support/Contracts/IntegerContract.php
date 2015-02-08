<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface IntegerContract
 *
 * @package im\Primitive\Support\Contracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface IntegerContract extends TypeContract {

    /**
     * @return mixed
     */
    public function toBool();

    /**
     * @return mixed
     */
    public function toFloat();

    /**
     * @return mixed
     */
    public function toString();

    /**
     * @return mixed
     */
    public function toContainer();
}

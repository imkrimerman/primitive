<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface BooleanContract
 *
 * @package im\Primitive\Support\Contracts
 */
interface BooleanContract extends TypeContract{

    /**
     * @return mixed
     */
    public function toInt();

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

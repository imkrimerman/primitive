<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface StringContract
 *
 * @package im\Primitive\Support\Contracts
 */
interface StringContract extends TypeContract {

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
    public function toFloat();

    /**
     * @return mixed
     */
    public function toContainer();
}

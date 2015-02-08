<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface ContainerContract
 *
 * @package im\Primitive\Support\Contracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface ContainerContract extends TypeContract {

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
    public function toBool();
}

<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface RevertableContract
 *
 * @package im\Primitive\Support\Contracts
 */
interface RevertableContract extends TypeContract {

    /**
     * @return mixed
     */
    public function revert();

    /**
     * @return mixed
     */
    public function save();

    /**
     * @return mixed
     */
    public function getClone();
}

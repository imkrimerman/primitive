<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface ArrayableContract
 *
 * @package im\Primitive\Support\Contracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface ArrayableContract {
    /**
     * Return array representation of instance.
     *
     * @return mixed
     */
    public function toArray();

    /**
     * Construct instance from array.
     *
     * @param array $array
     * @return mixed
     */
    public function fromArray(array $array = []);
}


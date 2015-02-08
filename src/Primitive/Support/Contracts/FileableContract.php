<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface FileableContract
 *
 * @package im\Primitive\Support\Contracts
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface FileableContract {

    /**
     * Write string representation of instance to file.
     *
     * @param string|StringContract $file
     * @param int|IntegerContract $jsonOptions
     * @return mixed
     */
    public function toFile($file, $jsonOptions = 0);

    /**
     * Construct instance from file.
     *
     * @param string|StringContract $file
     * @return mixed
     */
    public function fromFile($file);
}

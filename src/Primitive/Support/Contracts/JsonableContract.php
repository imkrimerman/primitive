<?php namespace im\Primitive\Support\Contracts;

/**
 * Interface JsonableContract
 *
 * @package im\Primitive\Support\Contracts
 * @author  Igor Krimerman <i.m.krimerman@gmail.com>
 */
interface JsonableContract {

    /**
     * Convert instance to json.
     *
     * @return string
     */
    public function toJson();

    /**
     * Construct instance from json
     *
     * @param string|StringContract $json
     * @return mixed
     */
    public function fromJson($json);
}


<?php namespace im\Primitive\Support\Contracts;


interface ArrayableInterface
{
    public function toArray();

    public function fromArray(array $array = array());
}


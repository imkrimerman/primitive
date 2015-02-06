<?php namespace im\Primitive\Support\Contracts;


interface RevertableContract
{
    public function revert();

    public function save();

    public function getClone();
}

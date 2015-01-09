<?php namespace im\Primitive\Support\Contracts;


interface RevertableInterface
{
    public function revert();

    public function save();
}

<?php namespace im\Primitive\Support\Contracts;


interface FileableInterface
{
    public function toFile($file);

    public function fromFile($file);
}

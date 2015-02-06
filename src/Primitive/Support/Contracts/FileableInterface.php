<?php namespace im\Primitive\Support\Contracts;


interface FileableInterface
{
    public function toFile($file, $jsonOptions = 0);

    public function fromFile($file);
}

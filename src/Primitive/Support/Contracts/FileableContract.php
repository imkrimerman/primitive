<?php namespace im\Primitive\Support\Contracts;


interface FileableContract
{
    public function toFile($file, $jsonOptions = 0);

    public function fromFile($file);
}

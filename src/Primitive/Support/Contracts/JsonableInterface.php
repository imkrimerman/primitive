<?php namespace im\Primitive\Support\Contracts;


interface JsonableInterface
{
    public function toJson();

    public function fromJson( $json );
}


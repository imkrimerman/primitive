<?php namespace im\Primitive\Support\Contracts;


interface JsonableContract
{
    public function toJson();

    public function fromJson( $json );
}


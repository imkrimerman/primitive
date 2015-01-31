<?php namespace im\Primitive\Support\Contracts;


interface BooleanInterface {

    public function toInt();

    public function toFloat();

    public function toString();

    public function toContainer();
}

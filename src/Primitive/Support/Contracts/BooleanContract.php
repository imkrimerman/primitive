<?php namespace im\Primitive\Support\Contracts;


interface BooleanContract {

    public function toInt();

    public function toFloat();

    public function toString();

    public function toContainer();
}

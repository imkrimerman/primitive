<?php namespace im\Primitive\Support\Contracts;


interface StringInterface {

    public function toInt();

    public function toBool();

    public function toFloat();

    public function toContainer();
}

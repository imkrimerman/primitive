<?php namespace im\Primitive\Support\Contracts;

interface TypeInterface {

    public function value();

    public function __invoke();

    public function __toString();
}

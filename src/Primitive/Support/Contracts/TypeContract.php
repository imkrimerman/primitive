<?php namespace im\Primitive\Support\Contracts;

interface TypeContract {

    public function value();

    public function __invoke();

    public function __toString();
}

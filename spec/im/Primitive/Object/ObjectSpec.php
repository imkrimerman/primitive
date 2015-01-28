<?php

namespace spec\im\Primitive\Object;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ObjectSpec extends ObjectBehavior
{
    protected $init;

    function let()
    {
        $this->init = [];

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\Object\Object');
    }
}

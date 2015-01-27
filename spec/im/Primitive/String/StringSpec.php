<?php

namespace spec\im\Primitive\String;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StringSpec extends ObjectBehavior
{
    protected $init;

    function let()
    {
        $this->init = 'foobar';

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\String\String');
    }

    /**
     * Abstract Type methods
     */

    function it_should_return_value_if_call_instance_like_function()
    {
        $this()->shouldBe($this->init);
    }

    function it_should_serialize()
    {
        $match = serialize($this->init);

        $this->serialize()->shouldBe($match);
    }

    function it_should_unserialize()
    {
        $serialized = serialize($this->init);

        $this->unserialize($serialized);
        $this->value()->shouldBe($this->init);
    }

    function it_should_make_new_instance()
    {
        $match = $this->init;

        $this->make($match)->value()->shouldBe($match);
    }

    function it_should_create_new_instance_statically()
    {
        $match = $this->init;

        static::create($match)->value()->shouldBe($match);
    }

    function it_should_have_helper_function_and_create_new_instance()
    {
        $match = $this->init;

        $this->set(string($match))->value()->shouldBe($match);
    }
}

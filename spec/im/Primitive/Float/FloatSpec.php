<?php

namespace spec\im\Primitive\Float;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FloatSpec extends ObjectBehavior
{
    protected $init;

    function let()
    {
        $this->init = 10.5;

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\Float\Float');
    }

    function it_should_construct_from_int()
    {
        $init = 1;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((float) $init);
    }

    function it_should_construct_from_float()
    {
        $init = 1.1;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo($init);
    }

    function it_should_construct_from_string()
    {
        $init = 'false';

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((float) $init);
    }

    function it_should_construct_from_bool()
    {
        $init = true;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((float) $init);
    }

    function it_should_construct_from_String_Type()
    {
        $init = 'false';

        $this->beConstructedWith(string($init));

        $this->value()->shouldBeEqualTo((float) $init);
    }

    function it_should_construct_from_Int_Type()
    {
        $init = 13;

        $this->beConstructedWith(int($init));

        $this->value()->shouldBeEqualTo((float) $init);
    }

    function it_should_construct_from_Float_Type()
    {
        $init = 12.3;

        $this->beConstructedWith(float($init));

        $this->value()->shouldBeEqualTo($init);
    }

    function it_should_construct_from_Bool_Type()
    {
        $init = false;

        $this->beConstructedWith(bool($init));

        $this->value()->shouldBeEqualTo((float) $init);
    }

    function it_should_call_method_if_it_looks_looks_variable()
    {
        $this->value->shouldBe($this->init);
    }

    function it_should_convert_to_Bool_Type()
    {
        $this->toBool()->shouldHaveType('im\Primitive\Bool\Bool');
    }

    function it_should_convert_to_Int_Type()
    {
        $this->toInt()->shouldHaveType('im\Primitive\Int\Int');
    }

    function it_should_convert_to_String_Type()
    {
        $this->toString()->shouldHaveType('im\Primitive\String\String');
    }

    function it_should_convert_to_Container_Type()
    {
        $this->toContainer()->shouldHaveType('im\Primitive\Container\Container');
    }

    function it_should_return_true_if_isTrue_called()
    {
        $result = (bool) $this->init ?: false;

        $this->isTrue()->shouldBe($result);
    }

    function it_should_return_false_if_isFalse_called()
    {
        $result = (bool) $this->init ? false: true;

        $this->isFalse()->shouldBe($result);
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

        $this->set(float($match))->value()->shouldBe($match);
    }
}

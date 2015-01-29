<?php namespace spec\im\Primitive\Bool;

use im\Primitive\Bool\Bool;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use im\Primitive\Support\Traits\RetrievableTrait;


class BoolSpec extends ObjectBehavior
{
    use RetrievableTrait;

    protected $init;

    function let()
    {
        date_default_timezone_set('UTC');

        $this->init = true;

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\Bool\Bool');
    }

    function it_should_construct_from_int()
    {
        $this->beConstructedWith(1);

        $this->value()->shouldBeEqualTo(true);
    }

    function it_should_construct_from_float()
    {
        $this->beConstructedWith(1.1);

        $this->value()->shouldBeEqualTo(true);
    }

    function it_should_construct_from_String_Type()
    {
        $this->beConstructedWith(string('false'));

        $this->value()->shouldBeEqualTo(false);
    }

    function it_should_construct_from_Int_Type()
    {
        $this->beConstructedWith(int(0));

        $this->value()->shouldBeEqualTo(false);
    }

    function it_should_construct_from_Float_Type()
    {
        $this->beConstructedWith(float(0.0));

        $this->value()->shouldBeEqualTo(false);
    }

    function it_should_construct_from_Bool_Type()
    {
        $this->beConstructedWith(bool('false'));

        $this->value()->shouldBeEqualTo(false);
    }

    function it_should_set_right_bool_from_string()
    {
        foreach ($this->getGrammar() as $rule => $bool)
        {
            $this->set($rule);

            $this->value()->shouldBeEqualTo($bool);
        }
    }

    function it_should_call_method_if_it_looks_looks_variable()
    {
        $this->value->shouldBe($this->init);

        $this->default->shouldBe(false);
    }

    function it_should_convert_to_Int_Type()
    {
        $this->toInt()->shouldHaveType('im\Primitive\Int\Int');
    }

    function it_should_convert_to_Float_Type()
    {
        $this->toFloat()->shouldHaveType('im\Primitive\Float\Float');
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
        $result = $this->init ?: false;

        $this->isTrue()->shouldBe($result);
    }

    function it_should_return_false_if_isFalse_called()
    {
        $result = $this->init ? false: true;

        $this->isFalse()->shouldBe($result);
    }

    function it_should_return_right_string_representation()
    {
        $result = $this->init ? 'true' : 'false';

        $this->__toString()->shouldBe($result);
    }

    /**
     * MacroableTrait Tests
     */

    function it_should_register_macro_and_save_it()
    {
        static::macro('testing', function()
        {
            return 'ok!';
        });

        static::hasMacro('testing')->shouldBe(true);
    }

    function it_should_throw_during_call_macro_statically_if_not_registered()
    {
        $this->shouldThrow('\BadMethodCallException')->duringNew();
    }

    function it_should_call_macro_statically()
    {
        static::macro('new', function()
        {
            return new Bool('false');
        });

        static::{'new'}()->shouldHaveType('im\Primitive\Bool\Bool');
    }

    function it_should_call_macro_not_statically()
    {
        static::macro('new', function()
        {
            return new Bool('false');
        });

        $this->new()->shouldHaveType('im\Primitive\Bool\Bool');
    }


    /**
     * Abstract Type methods
     */
    function it_should_return_inner_value()
    {
        $this->value()->shouldBeBool();
        $this->value()->shouldBeEqualTo($this->init);

        $this->get()->shouldBeBool();
        $this->get()->shouldBeEqualTo($this->init);
    }

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

        $this->set(bool($match))->value()->shouldBe($match);
    }
}

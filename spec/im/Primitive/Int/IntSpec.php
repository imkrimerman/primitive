<?php

namespace spec\im\Primitive\Int;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class IntSpec extends ObjectBehavior
{
    protected $init;

    function let()
    {
        $this->init = 10;

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\Int\Int');
    }

    /**
     * Construction and Conversion
     */

    function it_should_construct_from_int()
    {
        $init = 1;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo($init);
    }

    function it_should_construct_from_float()
    {
        $init = 1.1;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((int) $init);
    }

    function it_should_construct_from_string()
    {
        $init = 'false';

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((int) $init);
    }

    function it_should_construct_from_bool()
    {
        $init = true;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((int) $init);
    }

    function it_should_construct_from_String_Type()
    {
        $init = 'false';

        $this->beConstructedWith(string($init));

        $this->value()->shouldBeEqualTo((int) $init);
    }

    function it_should_construct_from_Int_Type()
    {
        $init = 13;

        $this->beConstructedWith(int($init));

        $this->value()->shouldBeEqualTo($init);
    }

    function it_should_construct_from_Float_Type()
    {
        $init = 12.3;

        $this->beConstructedWith(float($init));

        $this->value()->shouldBeEqualTo((int) $init);
    }

    function it_should_construct_from_Bool_Type()
    {
        $init = false;

        $this->beConstructedWith(bool($init));

        $this->value()->shouldBeEqualTo((int) $init);
    }

    function it_should_call_method_if_it_looks_looks_variable()
    {
        $this->value->shouldBe($this->init);
    }

    function it_should_convert_to_Bool_Type()
    {
        $this->toBool()->shouldHaveType('im\Primitive\Bool\Bool');
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

    /**
     * Math
     */

    function it_should_plus_values()
    {
        $plus = 10;

        $this->plus($plus)->value()->shouldBe($this->init + $plus);
    }

    function it_should_minus_values()
    {
        $minus = 5;

        $this->minus($minus)->value()->shouldBe($this->init - $minus);
    }

    function it_should_multiply_values()
    {
        $multiply = 10;

        $this->multiply($multiply)->value()->shouldBe($this->init * $multiply);
    }

    function it_should_divide_values()
    {
        $divide = 10;

        $this->divide($divide)->value()->shouldBe($this->init / $divide);
    }

    function it_should_modulo_values()
    {
        $modulo = 2;

        $this->modulo($modulo)->value()->shouldBe($this->init % $modulo);
    }

    function it_should_power_by_value()
    {
        $power = 2;

        $this->power($power)->value()->shouldBe(pow($this->init, $power));
    }

    function it_should_sqrt_by_value()
    {
        $sqrt = 2;

        $this->sqrt($sqrt)->value()->shouldBe(sqrt($this->init));
    }

    function it_should_abs_value()
    {
        $init = -20;

        $this->set($init)->abs()->value()->shouldBe(abs($init));
    }

    function it_should_return_sin_of_value()
    {
        $this->sin()->value()->shouldBe(sin($this->init));
    }

    function it_should_return_cos_of_value()
    {
        $this->cos()->value()->shouldBe(cos($this->init));
    }

    function it_should_return_tan_of_value()
    {
        $this->tan()->value()->shouldBe(tan($this->init));
    }

    function it_should_return_Float_Type_with_PI_value()
    {
        $this->pi()->value->shouldBe(pi());
    }

    //function it_should_return_factorial_of_value()
    //{
    //    $this->factorial()->value()->shouldBe($this->fact($this->init));
    //}

    function it_should_format_int_to_correct_float_string()
    {
        $formatted = $this->format();

        $formatted->shouldHaveType('im\Primitive\String\String');

        $formatted->value->shouldBe(number_format($this->init, 2, '.', ' '));
    }

    function it_should_return_length_of_value()
    {
        $this->length()->shouldBe(mb_strlen((string) $this->init));
    }

    function it_should_return_true_ifEqual()
    {
        $this->isEqual(int($this->init));
        $this->isEqual($this->init);
    }

    //function it_should_check_if_lessThen()
    //{
    //
    //}
    //
    //function it_should_check_if_lessThenOrEqual()
    //{
    //
    //}

    /**
     * Helpers
     */

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

        $this->set(int($match))->value()->shouldBe($match);
    }

    /**
     * Helper for test
     */

    /**
     * @param $n
     *
     * @return int
     */
    function fact($n)
    {
        return $n ? $n * $this->fact($n - 1) : 1;
    }
}
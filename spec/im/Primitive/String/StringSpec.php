<?php

namespace spec\im\Primitive\String;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Stringy\StaticStringy;


class StringSpec extends ObjectBehavior
{
    protected $init;

    function let()
    {
        $this->init = 'fooBar';

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\String\String');
    }


    /**
     * Construction and conversion
     */

    function it_should_construct_from_int()
    {
        $init = 1;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((string) $init);
    }

    function it_should_construct_from_float()
    {
        $init = 1.1;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo((string) $init);
    }

    function it_should_construct_from_string()
    {
        $init = 'false';

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo($init);
    }

    function it_should_construct_from_bool()
    {
        $init = true;

        $this->beConstructedWith($init);

        $this->value()->shouldBeEqualTo(($init ? 'true' : 'false'));
    }

    function it_should_construct_from_String_Type()
    {
        $init = 'false';

        $this->beConstructedWith(string($init));

        $this->value()->shouldBeEqualTo($init);
    }

    function it_should_construct_from_Int_Type()
    {
        $init = 13;

        $this->beConstructedWith(int($init));

        $this->value()->shouldBeEqualTo((string) $init);
    }

    function it_should_construct_from_Float_Type()
    {
        $init = 12.3;

        $this->beConstructedWith(float($init));

        $this->value()->shouldBeEqualTo((string) $init);
    }

    function it_should_construct_from_Bool_Type()
    {
        $init = false;

        $this->beConstructedWith(bool($init));

        $this->value()->shouldBeEqualTo(($init ? 'true' : 'false'));
    }

    function it_should_convert_to_Bool_Type()
    {
        $this->toBool()->shouldHaveType('im\Primitive\Bool\Bool');
    }

    function it_should_convert_to_Int_Type()
    {
        $this->toInt()->shouldHaveType('im\Primitive\Int\Int');
    }

    function it_should_convert_to_Float_Type()
    {
        $this->toFloat()->shouldHaveType('im\Primitive\Float\Float');
    }

    function it_should_convert_to_Container_Type()
    {
        $this->toContainer(true)->shouldHaveType('im\Primitive\Container\Container');
    }

    function it_should_return_value_with_get_method()
    {
        $this->get()->shouldBe($this->init);
    }

    function it_should_return_length()
    {
        $this->length()->shouldBe(mb_strlen($this->init));
    }

    function it_should_return_Container_with_all_chars()
    {
        $chars = $this->chars();

        $chars->shouldHaveType('im\Primitive\Container\Container');

        $chars->all()->shouldBe(StaticStringy::chars($this->init));
    }

    function it_should_append_any_type_to_inner_value()
    {
        $append = 'Baz';

        $this->append($append)->value()->shouldBe($this->init.$append);

        $this->set($this->init);
        $this->append(string($append))->value()->shouldBe($this->init.$append);

        $this->set($this->init);
        $this->append(bool(true))->value()->shouldBe($this->init.'true');

        $this->set($this->init);
        $this->append(container(['Bar', 'Baz']))->value()->shouldBe($this->init.'Bar'.'Baz');

        $this->set($this->init);
        $this->append(int(1))->value()->shouldBe($this->init.'1');

        $this->set($this->init);
        $this->append(float('1.04'))->value()->shouldBe($this->init.'1.04');
    }

    function it_should_prepend_any_type_to_inner_value()
    {
        $prepend = 'Baz';

        $this->prepend($prepend)->value()->shouldBe($prepend . $this->init);

        $this->set($this->init);
        $this->prepend(string($prepend))->value()->shouldBe($prepend . $this->init);

        $this->set($this->init);
        $this->prepend(bool(true))->value()->shouldBe('true' . $this->init);

        $this->set($this->init);
        $this->prepend(container(['Bar', 'Baz']))->value()->shouldBe('Bar' . 'Baz' . $this->init);

        $this->set($this->init);
        $this->prepend(int(1))->value()->shouldBe('1' . $this->init);

        $this->set($this->init);
        $this->prepend(float('1.04'))->value()->shouldBe('1.04' . $this->init);
    }

    function it_should_make_value_lower_case()
    {
        $this->lower()->value()->shouldBe(mb_strtolower($this->init));
    }

    function it_should_make_first_letter_lower_case()
    {
        $init = 'FooBar';

        $this->set($init)->lowerFirst()->value()->shouldBe(lcfirst($init));
    }

    function it_should_make_value_upper_case()
    {
        $this->upper()->value()->shouldBe(mb_strtoupper($this->init));
    }

    function it_should_make_first_letter_upper_case()
    {
        $init = 'fooBar';

        $this->set($init)->upperFirst()->value()->shouldBe(ucfirst($init));
    }

    function it_should_make_camel_case_and_upper_first_letter()
    {
        $init = 'foo bar';

        $this->set($init)->upperCamel()->value()->shouldBe('FooBar');
    }

    function it_should_make_title_case()
    {
        $init = 'foo bar';

        $this->set($init)->title()->value()->shouldBe('Foo Bar');
    }

    function it_should_make_came_case()
    {
        $init = 'foo bar';

        $this->set($init)->camel()->value()->shouldBe('fooBar');
    }

    function it_should_make_dashed_case()
    {
        $init = 'foo bar';

        $this->set($init)->dashed()->value()->shouldBe('foo-bar');
    }

    function it_should_make_snake_case()
    {
        $init = 'Foo bar';

        $this->set($init)->snake()->value()->shouldBe('foo_bar');
    }

    function it_should_make_studly_case()
    {
        $init = 'foo bar';

        $this->set($init)->studly()->value()->shouldBe('FooBar');
    }

    function it_should_swap_case()
    {
        $init = 'foo bar';

        $this->set($init)->swapCase()->value()->shouldBe('FOO BAR');

        $init = 'Foo Bar';

        $this->set($init)->swapCase()->value()->shouldBe('fOO bAR');
    }

    function it_should_make_human_readable_text()
    {
        $init = 'foo bar_id_baz';

        $this->set($init)->humanize()->value()->shouldBe('Foo bar baz');
    }

    function it_should_make_title_case_and_ignore_given_values()
    {
        $init = 'foo bar_baz ignore';

        $this->set($init)->titleize(['ignore'])->value()->shouldBe('Foo Bar_baz ignore');
    }

    function it_should_return_true_if_value_has_given_string()
    {
        $this->has('oB')->shouldBe(true);

        $this->has('ob', false)->shouldBe(true);
    }

    function it_should_return_true_if_value_has_any_of_given_string()
    {
        $this->hasAny(['bc','oB'])->shouldBe(true);

        $this->hasAny(['bc','dB'])->shouldBe(false);

        $this->hasAny(['cb','ob'], false)->shouldBe(true);
    }

    function it_should_return_true_if_value_has_all_of_given_string()
    {
        $this->hasAll(['fo','oB'])->shouldBe(true);

        $this->hasAll(['fo','oZ'])->shouldBe(false);

        $this->hasAll(['fo','ob'], false)->shouldBe(true);
    }

    function it_should_collapse_whitespaces()
    {
        $init = "\n\t".'foo          bar             baz' . "\n\t";

        $this->set($init)->collapseWhitespace()->value()->shouldBe('foo bar baz');
    }

    function it_should_return_ascii()
    {
        $init = 'ща_foo';

        $this->set($init)->toAscii()->value()->shouldBe('shcha_foo');
    }

    function it_should_replace_tabs_to_given_space_quantity()
    {
        $init = "\tfoo\t";

        $this->set($init)->toSpaces()->value()->shouldBe('    foo    ');

        $this->set($init)->toSpaces(2)->value()->shouldBe('  foo  ');
    }

    function it_should_replace_given_space_quantity_to_tab()
    {
        $init = "    foo    ";

        $this->set($init)->toTabs()->value()->shouldBe("\tfoo\t");

        $this->set($init)->toTabs(2)->value()->shouldBe("\t\tfoo\t\t");
    }

    function it_should_surrond_value_with_given_string()
    {
        $surround = 'Z';

        $this->surround($surround)->value()->shouldBe($surround.$this->init.$surround);
    }

    function it_should_insert_string_or_char_at_given_index()
    {
        $insert = 'insert';

        $this->insert($insert, 0)->value()->shouldBe($insert.$this->init);

        $this->insert($insert, 3)->value()->shouldBe('foo'.$insert.'Bar');

        $this->insert($insert, strlen($this->init) + 2)->value()->shouldBe($this->init);
    }

    function it_should_reverse_value()
    {
        $reverse = strrev($this->init);

        $this->reverse()->value()->shouldBe($reverse);
    }

    function it_should_return_char_at_given_index()
    {
        $this->at(1)->value()->shouldBe(substr($this->init, 1, 1));
    }

    function it_should_return_first_n_items()
    {
        $this->first(3)->value()->shouldBe(substr($this->init, 0, 3));
    }

    function it_should_return_last_n_items()
    {
        $this->last(3)->value()->shouldBe(substr($this->init, -3));
    }

    function it_should_ensure_that_left_part_of_value_has_given_string_if_not_than_add()
    {
        $left = substr($this->init, 0, 3);

        $this->ensureLeft($left)->value()->shouldBe($this->init);

        $this->ensureLeft('ensured')->value()->shouldBe('ensured'.$this->init);
    }

    function it_should_ensure_that_right_part_of_value_has_given_string_if_not_than_add()
    {
        $right = substr($this->init, -3);

        $this->ensureRight($right)->value()->shouldBe($this->init);

        $this->ensureRight('ensured')->value()->shouldBe($this->init.'ensured');
    }

    function it_should_replace_string()
    {
        $this->replace('foo', 'baz')->value()->shouldBe(str_replace('foo', 'baz', $this->init));
    }

    function it_should_replace_arrayable_of_replacements_in_string()
    {
        $array = ['f', 'o'];

        $container = container($array);

        $this->replace($array, 'baz')->value()->shouldBe(str_replace($array, 'baz', $this->init));

        $this->replace($container, 'baz')->value()->shouldBe(str_replace($container->all(), 'baz', $this->init));
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

<?php

namespace spec\im\Primitive\String;

use im\Primitive\Container\Container;
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
        $this->toContainer()->shouldHaveType('im\Primitive\Container\Container');
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

    function it_should_remove_chars_from_left()
    {
        $str = substr($this->init, 2);

        $this->removeLeft('fo')->value()->shouldBe($str);
    }

    function it_should_remove_chars_from_right()
    {
        $str = substr($this->init, 0, -2);

        $this->removeRight('ar')->value()->shouldBe($str);
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

    function it_should_check_if_value_starts_with_given_string()
    {
        $this->startsWith('foo')->shouldBe(true);
        $this->startsWith('Bar')->shouldBe(false);
        $this->startsWith(container(['foo', 'f']))->shouldBe(true);
    }

    function it_should_check_if_value_ends_with_given_string()
    {
        $this->endsWith('foo')->shouldBe(false);
        $this->endsWith('Bar')->shouldBe(true);
        $this->endsWith(container(['Bar', 'ar']))->shouldBe(true);
    }

    function it_should_check_if_string_is_matching_pattern()
    {
        $this->is('.*')->shouldBe(true);
    }

    function it_should_remove_duplicated_values_at_the_end_and_leave_one()
    {
        $this->finish('r')->value()->shouldBe($this->init);

        $this->set('dir//')->finish('/')->value()->shouldBe('dir/');
    }

    function it_should_cut_by_words_count()
    {
        $this->set('lorem ipsum dolor foo bar')->words(3)->value()->shouldBe('lorem ipsum dolor...');
    }

    function it_should_parse_callback_and_method()
    {
        $this->set('class@method')->parseCallback()->value()->shouldBe(['class', 'method']);
    }

    function it_should_return_random_string()
    {
        $this->random()->value()->shouldBeString();
    }

    function it_should_return_quick_random_string()
    {
        $this->quickRandom()->value()->shouldBeString();
    }

    function it_should_return_slug_with_ascii()
    {
        $match = (string) StaticStringy::slugify('Вот такой вот адрес');

        $this->set('Вот такой вот адрес')->slug()->value()->shouldBe($match);
    }

    function it_should_explode_value_in_container()
    {
        $explode = $this->explode('B');

        $explode->shouldHaveType(Container::class);

        $explode->all()->shouldBe(['foo', 'ar']);
    }

    function it_should_implode_arrayable_to_value()
    {
        $this->implode('', container(['foo', 'Bar']))->value()->shouldBe($this->init);

        $this->shouldThrow('InvalidArgumentException')->duringImplode('', true);
    }

    function it_should_trim_value_by_given_parameter()
    {
        $init = ' foo ';

        $this->set($init)->trim('front')->value()->shouldBe(ltrim($init));
        $this->set($init)->trim('back')->value()->shouldBe(rtrim($init));
        $this->set($init.' bar')->trim('all')->value()->shouldBe(trim($init).'bar');
        $this->set($init)->trim()->value()->shouldBe(trim($init));
    }

    function it_should_generate_random_uuid()
    {
        $uuid = $this->uuid();

        $uuid->value()->shouldBeString();

        $this->isUuid($uuid)->shouldBe(true);

        $this->set($uuid)->isUuid()->shouldBe(true);

        $this->uuid()->shouldNotBe($uuid);
    }

    function it_should_repeat_value()
    {
        $this->repeat(3)->value()->shouldBe(str_repeat($this->init, 3));
    }

    function it_should_shuffle_value()
    {
        $this->shuffle()->value()->shouldNotBe($this->init);
        $this->shuffle(false)->value()->shouldNotBe($this->init);
    }

    function it_should_split_words_in_Container()
    {
        $init = 'lorem ipsum dolorm';

        $words = $this->set($init)->wordSplit();

        $words->shouldHaveType(Container::class);

        $words->all()->shouldBe(str_word_count($init, 2));
    }

    function it_should_strip_tags()
    {
        $tags = '<div>'.$this->init.'</div>';

        $this->set($tags)->stripTags()->value()->shouldBe(strip_tags($tags));
    }

    function it_should_encode_to_base64()
    {
        $_64 = $this->base64();

        $_64->value()->shouldBe(base64_encode($this->init));
    }

    function it_should_init_from_base64_to_valid_string()
    {
        $_64 = base64_encode($this->init);

        $this->fromBase64($_64)->value()->shouldBe($this->init);
    }

    function it_should_encode_html_entities()
    {
        $tags = '<div>'.$this->init.'</div>';

        $this->set($tags)->toEntities()->value()->shouldBe(htmlentities($tags));
    }

    function it_should_decode_html_entities()
    {
        $tags = '<div>'.$this->init.'</div>';

        $this->set($tags)->fromEntities()->value()->shouldBe(html_entity_decode($tags));

        $this->fromEntities($tags)->value()->shouldBe(html_entity_decode($tags));
    }

    function it_should_echo_value_with_append_and_prepend()
    {
        ob_start();

        $this->say('me', 'you');

        $said = ob_get_contents();

        $this->set($said)->value()->shouldBe('me'.$this->init.'you');

        ob_end_clean();
    }

    function it_should_echo_value()
    {
        ob_start();

        $this->say(new \stdClass(), new \stdClass());

        $said = ob_get_contents();

        $this->set($said)->value()->shouldBe($this->init);

        ob_end_clean();
    }

    function it_should_cut_value()
    {
        $this->cut(2)->value()->shouldBe(substr($this->init, 2));
        $this->cut(2, 1)->value()->shouldBe(substr($this->init, 2, 1));
    }

    function it_should_limit_letters_quantity_to_given()
    {
        $this->limit(3)->value()->shouldBe(substr($this->init, 0, 3).'...');
    }

    function it_should_limit_letters_quantity_to_given_but_check_for_words_cut()
    {
        $this->limitSafe(3)->value()->shouldBe('...');
    }

    function it_should_parse_vars_from_string()
    {
        $init = 'http://localhost:8000/?XDEBUG_SESSION_START=11857';

        $vars = $this->set($init)->toVars();

        $vars->shouldHaveType(Container::class);

        $expect = [];
        mb_parse_str($init, $expect);

        $vars->all()->shouldBe($expect);
    }

    function it_should_clean_string_from_tags_entities_and_trim()
    {
        $init = ' <div>'.$this->init.'</div>& ';

        $this->set($init)->clean()->value()->shouldBe(trim(htmlentities(strip_tags($init))));
    }

    function it_should_reset_inner_string()
    {
        $this->reset()->value()->shouldBe('');
    }

    function it_should_compress_string()
    {
        $this->compress()->value()->shouldBe(gzcompress($this->init));
    }

    function it_should_uncompress_string()
    {
        $str = gzcompress($this->init);

        $this->set($str)->uncompress()->value()->shouldBe($this->init);

        $this->set($this->init)->uncompress($str)->value()->shouldBe($this->init);
    }

    function it_should_return_encrypted_string()
    {
        $encrypted = $this->encrypt();

        $encrypted->value()->shouldBeString();
    }

    function it_should_construct_from_encrypted_string()
    {
        $encrypted = $this->encrypt();

        $this->fromEncrypted($encrypted)->value()->shouldBe($this->init);
    }

    function it_should_return_length_of_string()
    {
        $this->length()->shouldBe(mb_strlen($this->init));
    }

    function it_should_return_inner_string()
    {
        $this->all()->shouldBe($this->init);
    }

    function it_should_get_file_contents_if_is_file()
    {
        $this->shouldThrow('im\Primitive\String\Exceptions\StringException')->duringContents();

        $this->set(__DIR__.'/../Container/data.json')->contents()->isJson()->shouldBe(true);
    }

    function it_should_check_if_string_is_empty()
    {
        $this->isEmpty()->shouldBe(false);

        $this->set('')->isEmpty()->shouldBe(true);
    }

    function it_should_check_if_string_is_not_empty()
    {
        $this->isNotEmpty()->shouldBe(true);

        $this->set('')->isNotEmpty()->shouldBe(false);
    }

    function it_should_check_if_string_is_alpha()
    {
        $this->isAlpha()->shouldBe(true);

        $this->set('123qwe')->isAlpha()->shouldBe(false);
    }

    function it_should_check_if_string_is_alphanumeric()
    {
        $this->isAlphanumeric()->shouldBe(true);

        $this->set('123qwe')->isAlphanumeric()->shouldBe(true);

        $this->set('./../')->isAlphanumeric()->shouldBe(false);
    }

    function it_should_check_if_string_is_whitespace_chars()
    {
        $this->isWhitespaces()->shouldBe(false);

        $this->set("\n\t  ")->isWhitespaces()->shouldBe(true);
    }

    function it_should_check_if_string_is_hexadecimal()
    {
        $this->isHex()->shouldBe(false);

        $this->set('2AF3')->isHex()->shouldBe(true);
    }

    function it_should_check_if_string_is_upper()
    {
        $this->isUpper()->shouldBe(false);

        $this->upper()->isUpper()->shouldBe(true);
    }

    function it_should_check_if_string_is_lower()
    {
        $this->isLower()->shouldBe(false);

        $this->lower()->isLower()->shouldBe(true);

        $this->upper()->isLower()->shouldBe(false);
    }

    function it_should_check_if_is_uuid()
    {
        $uuid = $this->uuid();

        $uuid->isUuid()->shouldBe(true);

        $this->set('asdaa234-sd34-df35-vd2f-ptuqwv45ct56')->isUuid()->shouldBe(false);
    }

    function it_should_check_if_is_json()
    {
        $this->isJson()->shouldBe(false);

        $this->set('{"name":"fooBar"}')->isJson()->shouldBe(true);
    }

    function it_should_check_if_is_file()
    {
        $this->isFile()->shouldBe(false);

        $this->set(__DIR__.'/../Container/data.json')->isFile()->shouldBe(true);
    }

    function it_should_check_if_is_serialized()
    {
        $this->isSerialized()->shouldBe(false);

        $this->set('a:4:{s:4:"name";s:4:"John";s:7:"surname";s:3:"Doe";s:5:"email";s:19:"johndoe@example.com";s:4:"wife";a:4:{s:4:"name";s:4:"Jane";s:7:"surname";s:3:"Doe";s:5:"email";s:19:"janedoe@example.com";s:5:"hobby";s:5:"music";}}
')->isSerialized()->shouldBe(true);
    }

    function it_should_auto_convert_to_string()
    {
        $this->__toString()->shouldBe($this->init);
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

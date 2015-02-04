<?php

namespace spec\im\Primitive\Object;

use im\Primitive\Container\Container;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ObjectSpec extends ObjectBehavior
{
    protected $init;

    function let()
    {
        $this->init = ['name' => 'fooBar', 'surname' => 'Baz'];

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\Object\Object');
    }

    /**
     * Construction and conversion
     */
    function it_should_construct_properly_from_array()
    {
        $initializer = ['foo', 'bar', 'name' => 'fooBar'];

        $this->fromArray($initializer)->get('name')->shouldBe('fooBar');

        $this->length()->shouldBe(count($this->init));
    }

    function it_should_construct_from_json()
    {
        $initializer = string('{"key": "value"}');

        $this->beConstructedWith($initializer);

        $this->has('key')->shouldBe(true);
    }

    function it_should_construct_from_file_with_json()
    {
        $initializer = string(__DIR__.DIRECTORY_SEPARATOR.
                                '..'.DIRECTORY_SEPARATOR.
                                    'Container'.DIRECTORY_SEPARATOR.
                                        'data.json');

        $this->beConstructedWith($initializer);

        $this->has('name')->shouldBe(true);
    }

    function it_should_constuct_from_file()
    {
        $initializer = string(__DIR__.DIRECTORY_SEPARATOR.
                                '..'.DIRECTORY_SEPARATOR.
                                    'Container'.DIRECTORY_SEPARATOR.
                                        'data.json');

        $this->fromFile($initializer)->has('name')->shouldBe(true);
    }

    function it_should_put_inner_contents_to_file()
    {
        $this->toFile(__DIR__.DIRECTORY_SEPARATOR.'object.im')->shouldBe(true);
    }

    function it_should_convert_to_Container_Type()
    {
        $this->toContainer()->shouldHaveType('im\Primitive\Container\Container');
    }

    function it_should_convert_to_String_Type()
    {
        $this->toString()->shouldHaveType('im\Primitive\String\String');
    }

    function it_should_dynamically_set_property()
    {
        $value = 'new field';

        $this->field = $value;

        $this->get('field')->shouldBe($value);
    }

    function it_should_dynamically_get_value()
    {
        $this->name->shouldBe($this->init['name']);
    }

    function it_should_set_and_get_value_with_dot_notation()
    {
        $value = 'new field';

        $this->set('field.array', $value)->get('field.array')->shouldBe($value);

        $this->{'array.key'} = $value;

        $this->get('array.key')->shouldBe($value);
    }

    function it_should_check_if_has_property_with_dot_notation()
    {
        $this->has('name')->shouldBe(true);

        $this->set('a.a', 'value')->has('a.a');
    }

    function it_should_forget_value_by_property_with_dot_notation()
    {
        $this->forget('name')->has('name')->shouldBe(false);

        $this->set('a.a', 'value')->has('a.a')->shouldBe(true);

        $this->forget('a.a')->has('a.a')->shouldBe(false);

        $this->has('a')->shouldBe(true);
    }

    function it_should_return_length()
    {
        $this->length()->shouldBe(count($this->init));
    }

    function it_should_return_inner_value()
    {
        $this->value()->shouldBe($this->init);
    }

    function it_should_auto_convert_to_string()
    {
        $this->__toString()->shouldBe(json_encode($this->init));
    }

    function it_should_convert_to_json()
    {
        $this->toJson()->shouldBe(json_encode($this->init));
    }

    function it_should_convert_to_array()
    {
        $this->toArray()->shouldBe($this->init);
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
        $property = 'object';
        $value = object();

        $this->set($property, $value)->get($property)->shouldBe($value);
    }
}

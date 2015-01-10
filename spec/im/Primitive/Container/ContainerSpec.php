<?php namespace spec\im\Primitive\Container;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContainerSpec extends ObjectBehavior
{
    protected $initializer;

    function let()
    {
        $this->initializer = [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'johndoe@example.com',
            'wife' => ['name' => 'Jane',
                       'surname' => 'Doe',
                       'email' => 'janedoe@example.com']
        ];

        $this->beConstructedWith($this->initializer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\Container\Container');
    }

    function it_should_construct_from_array()
    {
        $initializer = ['foo', 'bar', 'baz'];

        $this->fromArray($initializer);

        $this->all()->shouldHaveCount(3);
    }

    function it_should_construct_from_json()
    {
        $initializer = '{"key": "value"}';

        $this->fromJson($initializer);

        $this->all()->shouldHaveCount(1);

        $this->has('key')->shouldBe(true);
    }

    function it_should_push_item_without_key_to_Container()
    {
        $this->push('newItem');

        $this->hasValue('newItem')->shouldBe(true);

        $this->checkLength();
    }

    function it_should_push_item_with_key_to_Container()
    {
        $this->push('newItem', 'newKey');

        $this->has('newKey')->shouldBe(true);
        $this->hasValue('newItem')->shouldBe(true);

        $this->checkLength();
    }

    function it_should_push_item_with_dot_key_and_create_multi_array()
    {
        $this->push('newItem', 'new.key');

        $this->has('new')->shouldBe(true);
        $this->hasValue('newItem')->shouldBe(true);

        $this->checkLength();
    }

    protected function checkLength($add = 1)
    {
        $this->all()->shouldHaveCount(count($this->initializer) + $add);
        $this->length->shouldBe(count($this->initializer) + $add);
    }
}

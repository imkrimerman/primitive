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
                       'email' => 'janedoe@example.com',
                        'hobby' =>'music']
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

    function it_should_call_method_with_magic_get_if_it_looks_like_variable()
    {
        $this->pop;

        $this->minusLengthCheck();
    }

    function it_should_push_item_without_key_to_Container()
    {
        $this->push('newItem');

        $this->hasValue('newItem')->shouldBe(true);

        $this->plusLengthCheck();
    }

    function it_should_push_item_with_key_to_Container()
    {
        $this->push('newItem', 'newKey');

        $this->has('newKey')->shouldBe(true);
        $this->hasValue('newItem')->shouldBe(true);

        $this->plusLengthCheck();
    }

    function it_should_push_item_with_dot_key_and_create_multi_array_in_Container()
    {
        $this->push('newItem', 'new.key');

        $this->has('new')->shouldBe(true);
        $this->hasValue('newItem')->shouldBe(true);

        $this->plusLengthCheck();
    }

    function it_should_pop_item_from_Container()
    {
        $this->pop();

        $this->minusLengthCheck();
    }

    function it_should_unshift_item_to_the_first_index()
    {
        $match = $this->initializer;

        array_unshift($match, 'newItem');

        $this->unshift('newItem')->all()->shouldBe($match);

        $this->plusLengthCheck();
    }

    function it_should_shift_item_from_the_first_index()
    {
        $this->shift();

        $this->minusLengthCheck();
    }

    function it_should_find_index_of_given_key()
    {
        $this->index('John')->shouldBe('name');
        $this->index('Doe')->shouldBe('surname');

        $this->index('fake')->shouldBe(false);
    }

    function it_should_return_true_if_Container_has_key()
    {
        $this->has('name')->shouldBe(true);

        $this->has('wife.hobby')->shouldBe(true);
    }

    function it_should_return_first_key_from_Container()
    {
        $this->firstKey()->shouldBe('name');

        $this->fromArray([]);

        $this->shouldThrow('im\Primitive\Container\Exceptions\EmptyContainerException')->duringFirstKey();
    }

    function it_should_return_last_key_from_Container()
    {
        $this->lastKey()->shouldBe('wife');

        $this->fromArray([]);

        $this->shouldThrow('im\Primitive\Container\Exceptions\EmptyContainerException')->duringLastKey();
    }

    function it_should_return_first_value_from_Container()
    {
        $this->first()->shouldReturn('John');
    }

    function it_should_return_last_value_from_Container()
    {
        $this->last()->shouldReturn(['name' => 'Jane',
                                     'surname' => 'Doe',
                                     'email' => 'janedoe@example.com',
                                     'hobby' =>'music']);
    }

    protected function checkLength()
    {
        $this->all()->shouldHaveCount(count($this->initializer));
        $this->length->shouldBe(count($this->initializer));
    }

    /**
     * @param $add
     */
    protected function plusLengthCheck($add = 1)
    {
        $this->all()->shouldHaveCount(count($this->initializer) + $add);
        $this->length->shouldBe(count($this->initializer) + $add);
    }

    /**
     * @param $add
     */
    protected function minusLengthCheck($add = 1)
    {
        $this->all()->shouldHaveCount(count($this->initializer) - $add);
        $this->length->shouldBe(count($this->initializer) - $add);
    }
}

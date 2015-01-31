<?php

namespace spec\im\Primitive\Container;

use im\Primitive\Container\ContainerFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RevertableContainerSpec extends ObjectBehavior
{
    protected $init;

    function let()
    {
        $this->init = [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'johndoe@example.com',
            'wife' => ['name' => 'Jane',
                       'surname' => 'Doe',
                       'email' => 'janedoe@example.com',
                       'hobby' =>'music']
        ];

        $this->beConstructedWith($this->init);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('im\Primitive\Container\RevertableContainer');
    }

    function it_should_clone_items_after_initialize()
    {
        $this->getClone()->shouldBe($this->init);
    }

    function it_should_save_state_of_items_to_clone()
    {
        $this->forget('wife')->save();

        unset($this->init['wife']);

        $this->getClone()->shouldBe($this->init);
    }

    function it_should_revert_state_from_clone()
    {
        $this->save()->forget('wife');

        $init = $this->init;
        unset($init['wife']);

        $this->all->shouldBe($init);

        $this->revert()->all->shouldBe($this->init);
    }

    function it_should_return_inner_value()
    {
        $this->value()->shouldBe($this->init);
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

        $this->fromArray(container($match, ContainerFactory::REVERTABLE)->all())->value()->shouldBe($match);
    }
}

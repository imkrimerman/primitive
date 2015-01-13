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

        $this->all->shouldBeEqualTo(['key' => 'value']);
    }

    function it_should_construct_from_file_with_json()
    {
        $initializer = __DIR__ . DIRECTORY_SEPARATOR . 'data.json';

        $this->fromString($initializer);

        $this->all->shouldBeEqualTo($this->initializer);

        $this->has('name')->shouldBe(true);

        $this->lengthCheck();
    }

    function it_should_recognize_and_construct_from_string_with_path_or_json()
    {
        /**
         * Json
         */
        $initializer = '{"key": "value"}';

        $this->fromString($initializer);

        $this->all()->shouldHaveCount(1);

        $this->has('key')->shouldBe(true);

        $this->all->shouldBeEqualTo(['key' => 'value']);

        /**
         * File with Json
         */
        $initializer = __DIR__ . DIRECTORY_SEPARATOR . 'data.json';

        $this->fromString($initializer);

        $this->all->shouldBeEqualTo($this->initializer);

        $this->has('name')->shouldBe(true);

        $this->lengthCheck();

        /**
         * File with serialized data
         */
        $initializer = __DIR__ . DIRECTORY_SEPARATOR . 'serialized.data';

        $this->fromString($initializer);

        $this->all->shouldBeEqualTo($this->initializer);

        $this->has('name')->shouldBe(true);

        $this->lengthCheck();
    }

    function it_should_call_method_with_magic_get_if_it_looks_like_variable()
    {
        $this->pop;

        $this->shift;

        //$this->shouldThrow('\BadMethodCallException')->during('where');

        $this->has('name')->shouldBe(false);

        $this->minusLengthCheck(2);
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

    function it_should_unique_Container_items()
    {
        $initializer = ['foo', 'bar', 'bar', 'bar'];

        $this->fromArray($initializer);

        $this->unique()->all()->shouldBeEqualTo(['foo', 'bar']);
    }

    function it_should_return_all_keys_from_Container()
    {
        $this->keys()->all()->shouldBe(['name', 'surname', 'email', 'wife']);
    }

    function it_should_return_all_values_from_Container()
    {
        $this->values()->all()->shouldBeEqualTo(
            [
                0 => 'John',
                1 => 'Doe',
                2 => 'johndoe@example.com',
                3 => [
                    'name' => 'Jane',
                    'surname' => 'Doe',
                    'email' => 'janedoe@example.com',
                    'hobby' =>'music'
                ]
            ]);
    }

    function it_should_divide_keys_and_values()
    {
        $this->divide()->all()->shouldBeEqualTo(
            [
                [
                    'name',
                    'surname',
                    'email',
                    'wife'
                ],
                [
                    0 => 'John',
                    1 => 'Doe',
                    2 => 'johndoe@example.com',
                    3 => [
                        'name' => 'Jane',
                        'surname' => 'Doe',
                        'email' => 'janedoe@example.com',
                        'hobby' =>'music'
                    ]
                ]
            ]
        );
    }

    function it_should_shuffle_items()
    {
        $this->shuffle()->shouldNotBeEqualTo($this->initializer);

        $this->lengthCheck();
    }

    function it_should_implode_items_even_if_its_multi_dimensional()
    {
        $this->implode()->shouldBeEqualTo('John Doe johndoe@example.com Jane Doe janedoe@example.com music');
    }

    function it_should_join_values_by_key_from_second_level()
    {
        $this->fromArray(
            [
                [
                    'name' => 'John',
                    'surname' => 'Doe',
                    'email' => 'johndoe@example.com'
                ],
                [
                    'name' => 'Jane',
                    'surname' => 'Doe',
                    'email' => 'janedoe@example.com'
                ]
            ]
        );

        $this->join('name')->shouldBeEqualTo('JohnJane');
    }

    function it_should_find_all_second_level_values_by_key_and_return_array()
    {
        $this->lists('hobby')->shouldBe(['music']);

        $this->lists('name')->shouldBe(['Jane']);

        $this->lengthCheck();
    }

    function it_should_split_items_into_chunks()
    {
        $this->chunk()->shouldHaveType('im\Primitive\Container\Container');

        $this->revert();

        $this->chunk(3)->all()->shouldBe(
            [
                [
                    'John',
                    'Doe',
                    'johndoe@example.com'
                ],
                [
                    [
                        'name' => 'Jane',
                        'surname' => 'Doe',
                        'email' => 'janedoe@example.com',
                        'hobby' =>'music'
                    ]
                ]
            ]
        );
    }

    function it_should_combine_given_keys_with_inner_values()
    {
        $keys = [
            'first', 'second', 'third', 'forth'
        ];

        $this->combine($keys)->all()->shouldBe(
            [
                'first' => 'John',
                'second' => 'Doe',
                'third' => 'johndoe@example.com',
                'forth' => [
                    'name' => 'Jane',
                    'surname' => 'Doe',
                    'email' => 'janedoe@example.com',
                    'hobby' =>'music'
                ]
            ]
        );

        $this->revert();

        $keys = [
            'first', 'second', 'third', 'forth'
        ];

        $this->combine($keys, 'keys')->all()->shouldBe(
            [
                'first' => 'John',
                'second' => 'Doe',
                'third' => 'johndoe@example.com',
                'forth' => [
                    'name' => 'Jane',
                    'surname' => 'Doe',
                    'email' => 'janedoe@example.com',
                    'hobby' =>'music'
                ]
            ]
        );
    }

    function it_should_combine_given_values_with_inner_keys()
    {
        $values = [
            'first', 'second', 'third', 'forth'
        ];

        $this->combine($values, 'values')->all()->shouldBe(
            [
                'name' => 'first',
                'surname' => 'second',
                'email' => 'third',
                'wife' => 'forth'
            ]
        );
    }

//    function it_should_throw_exception_if_length_of_give_array_is_not_equal_with_inner_length()
//    {
//        $keys = [
//            'first', 'second', 'third', 'forth', 'fifth'
//        ];
//
//        $this->shouldThrow('im\Primitive\Container\Exceptions\BadLengthException')
//             ->duringCombine($keys, 'keys');
//    }

    function it_should_throw_exception_if_given_wrong_second_param_to_combine()
    {
        $keys = [
            'first', 'second', 'third', 'forth'
        ];

        $this->shouldThrow('im\Primitive\Container\Exceptions\BadContainerMethodArgumentException')
             ->duringCombine($keys, 'foo');
    }

    function it_should_filter_items_with_callback_in_Container()
    {
        $this->filter('is_int')->all()->shouldBe([]);

        $this->first()->shouldBeEqualTo('John');

        $this->lengthCheck();
    }

    function it_should_traverse_through_each_item()
    {
        $this->each(function( & $value)
        {
            if ( ! is_array($value)) $value .= 'ok';

        })->first()->shouldBeEqualTo('John');

        $this->lengthCheck();
    }

    function it_should_traverse_through_items_and_map_them()
    {
        $this->map(function( & $value)
        {
            if ( ! is_array($value)) return $value .= 'Ok';

            return $value;

        })->first()->shouldBe('JohnOk');

        $this->lengthCheck();
    }

    function it_should_walk_through_all_first_level_items()
    {
        $this->walk(function( & $value)
        {
            if ( ! is_array($value)) $value .= 'Ok';

        })->first()->shouldBe('JohnOk');

        $this->lengthCheck();
    }

    function it_should_walk_through_all_items_recursive()
    {
        $this->walk(function( & $value)
        {
            $value .= 'Ok';

        }, true)->get('wife.hobby')->shouldBe('musicOk');

        $this->lengthCheck();
    }

    function it_should_walk_through_all_items_recursive_and_take_keys_in_callback()
    {
        $this->walk(function( & $value, $key)
        {
            $value .= "-{$key}";

        }, true)->get('wife.hobby')->shouldBe('music-hobby');

        $this->lengthCheck();
    }

    function it_should_walk_through_all_items_and_receive_userdata_in_callback_passed_as_third_param()
    {
        $this->walk(function( & $value, $key, $userdata)
        {
            $value .= "-{$key}-{$userdata}";

        }, true, 'USERDATA')->get('wife.hobby')->shouldBe('music-hobby-USERDATA');

        $this->lengthCheck();
    }

    function it_should_merge_items_with_given_array_and_leave_length_the_same()
    {
        $this->merge(['name' => 'Mike'])->first()->shouldBe('Mike');

        $this->lengthCheck();
    }

    function it_should_merge_items_with_given_array_and_recalculateLength()
    {
        $this->merge(['name' => 'Mike', 'mother' => 'Linda'])->length->shouldBe(count($this->initializer) + 1);
    }

    function it_should_merge_items_by_key_with_given_array_and_leave_length_the_same()
    {
        $this->merge(['name' => 'Mike'], 'wife')->get('wife.name')->shouldBe('Mike');

        $this->lengthCheck();
    }

    function it_should_increase_Container_length_and_add_given_value_to_new_indexes()
    {
        $this->increase('3', 'foo')->get(2)->shouldBe('foo');

        $this->plusLengthCheck(3);
    }

    function it_should_return_one_random_key_from_items_keys()
    {
        $this->randomKey()->shouldBeInRange($this->keys()->all());

        $this->lengthCheck();
    }

    function it_should_return_array_of_exact_number_of_random_keys_from_item_keys_but_not_larger_than_Container_length()
    {
        $this->randomKey(3)->shouldBeArrayAndExactLengthOf(3);

        $this->lengthCheck();
    }

    function it_should_return_random_value_from_Container()
    {
        $this->random()->shouldBeInRange($this->values()->all());

        $this->lengthCheck();
    }

    function it_should_return_array_of_exact_number_of_random_values_but_not_larger_than_Container_length()
    {
        $this->random(3)->shouldBeArrayAndExactLengthOf(3);

        $this->lengthCheck();
    }

    function it_should_cut_all_items_after_given_offset()
    {
        $this->cut(1)->firstKey()->shouldBeEqualTo('surname');

        $this->minusLengthCheck();
    }

    function it_should_cut_items_between_given_offset_and_length()
    {
        $this->cut(1, 2)->lastKey()->shouldBeEqualTo('email');

        $this->minusLengthCheck(2);
    }

    protected function lengthCheck()
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

    public function getMatchers()
    {
        return [

            'beInRange' => function($key, $match)
            {
                return in_array($key, $match);
            },

            'beArrayAndExactLengthOf' => function($array, $count)
            {
                return is_array($array) && $count === count($array);
            }

        ];
    }
}

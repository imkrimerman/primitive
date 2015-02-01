<?php namespace spec\im\Primitive\Container;

use JWT;
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

        $this->all()->shouldHaveCount(count($initializer));
    }

    function it_should_construct_from_json()
    {
        $initializer = '{"key": "value"}';

        $this->fromJson($initializer);

        $this->all()->shouldHaveCount(count(json_decode($initializer, true)));

        $this->has('key')->shouldBe(true);

        $this->all()->shouldBeEqualTo(['key' => 'value']);
    }

    function it_should_construct_from_file_with_json()
    {
        $initializer = __DIR__ . DIRECTORY_SEPARATOR . 'data.json';

        $this->fromFile($initializer);

        $this->all()->shouldBeEqualTo($this->initializer);

        $this->has('name')->shouldBe(true);

        $this->lengthCheck();
    }

    function it_should_call_method_with_magic_get_if_it_looks_like_variable()
    {
        $this->pop;

        $this->shift;

        $this->has('name')->shouldBe(false);

        $this->minusLengthCheck(2);
    }

    function it_should_get_value_by_key_with_dot_notation()
    {
        $obj = new \stdClass();
        $obj->value = 'added';

        $this->set('new.value', 'added');

        $this->get('new.value')->shouldBe('added');
    }

    function it_should_set_item_by_key_with_dot_notation()
    {
        $match = $this->initializer;

        $match['wife']['like'] = 'husband';

        $this->set('wife.like', 'husband')->all()->shouldBe($match);

        $obj = new \stdClass();
        $obj->value = 'added';

        $this->set('new.value', 'added');

        $this->get('new.value')->shouldBe('added');
    }

    function it_should_push_item_without_key_to_Container()
    {
        $this->push('newItem');

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

        $this->prepend('newItem')->all()->shouldBe($match);

        $this->plusLengthCheck();
    }

    function it_should_shift_item_from_the_first_index()
    {
        $this->shift();

        $this->minusLengthCheck();
    }

    function it_should_find_index_of_given_key()
    {
        $this->search('John')->shouldBe('name');
        $this->search('Doe')->shouldBe('surname');

        $this->search('fake')->shouldBe(false);
    }

    function it_should_return_true_if_Container_has_key()
    {
        $this->has('name')->shouldBe(true);

        $this->has('wife.hobby')->shouldBe(true);

        $obj = new \stdClass();
        $obj->value = 15;

        $this->initializer['new'] = $obj;

        $this->fromArray($this->initializer)->has('new.value')->shouldBe(true);

        $this->has('noKey')->shouldBe(false);
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

    function it_should_return_first_value_that_passes_truth_test()
    {
        $this->firstWhere(function($key)
        {
            return $key == 'surname';

        })->shouldBeEqualTo('Doe');

        $this->lengthCheck();
    }

    function it_should_return_last_value_that_passes_truth_test()
    {
        $this->lastWhere(function($key)
        {
            return $key == 'surname';

        })->shouldBeEqualTo('Doe');

        $this->lengthCheck();
    }

    function it_should_return_last_value_from_Container()
    {
        $this->last()->shouldReturn(['name' => 'Jane',
                                     'surname' => 'Doe',
                                     'email' => 'janedoe@example.com',
                                     'hobby' =>'music']);
    }

    function it_should_assign_keys_by_inner_arrays_key_value()
    {
        $this->fromArray([
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
        ]);

        $keysBy = $this->keysByField('name');

        $keysBy->all()->shouldBeEqualTo([
            'John' => [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'johndoe@example.com'
            ],
            'Jane' => [
                'name' => 'Jane',
                'surname' => 'Doe',
                'email' => 'janedoe@example.com'
            ]
        ]);
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

    function it_should_return_items_with_only_numeric_keys()
    {
        $init = [
            0 => 'John',
            1 => 'Doe',
            'email' => 'johndoe@example.com',
            3 => [
                'name' => 'Jane',
                'surname' => 'Doe',
                'email' => 'janedoe@example.com',
                'hobby' =>'music'
            ]
        ];

        $match = $init;

        unset($match['email']);

        $this->fromArray($init)->numericKeys()->all()->shouldBe($match);
    }

    function it_should_return_items_with_only_not_numeric_keys()
    {
        $init = [
            0 => 'John',
            1 => 'Doe',
            'email' => 'johndoe@example.com',
            3 => [
                'name' => 'Jane',
                'surname' => 'Doe',
                'email' => 'janedoe@example.com',
                'hobby' =>'music'
            ]
        ];

        $this->fromArray($init)->notNumericKeys()->all()->shouldBe(['email' => 'johndoe@example.com']);
    }

    function it_should_shuffle_items()
    {
        $this->shuffle()->shouldNotBeEqualTo($this->initializer);

        $this->lengthCheck();
    }

    function it_should_implode_items_even_if_its_multi_dimensional()
    {
        $this->join()->get()->shouldBeEqualTo('JohnDoejohndoe@example.comJaneDoejanedoe@example.commusic');
    }

    function it_should_join_values_by_key_from_second_level()
    {
        $this->fromArray(
            [
                [
                    'name' => 'John',
                    'surname' => 'Doe',
                    'email' => 'johndoe@example.com',
                ],
                [
                    'name' => 'Jane',
                    'surname' => 'Doe',
                    'email' => 'janedoe@example.com',
                ]
            ]
        );

        $this->joinByKey('name')->get()->shouldBeEqualTo('JohnJane');
    }

    function it_should_find_all_second_level_values_by_key_and_return_array()
    {
        $this->lists('hobby')->all()->shouldBe(['music']);

        $this->lists('name')->all()->shouldBe(['Jane']);

        $this->lengthCheck();
    }

    function it_should_split_items_into_chunks()
    {
        $chunks = $this->flatten()->chunk(2)->all();

        foreach ($chunks as $chunk)
        {
            $chunk->shouldHaveType('im\Primitive\Container\Container');
        }
    }

    function it_should_combine_given_keys_with_inner_values_without_second_argument()
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
    }

    function it_should_combine_given_keys_with_inner_values_with_second_argument_given()
    {
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

        $this->shouldThrow('\BadMethodCallException')
             ->duringCombine($keys, 'foo');
    }

    function it_should_filter_items_with_callback_in_Container()
    {
        $filtered = $this->filter('is_int');
        $filtered->all()->shouldBe([]);

        $filtered->shouldHaveCount(0);

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

    function it_should_map_items_them_and_return_new_Container()
    {
        $map = $this->map(function( & $value)
        {
            if ( ! is_array($value)) return $value .= 'Ok';

            return $value;
        });

        $map->shouldHaveType('im\Primitive\Container\Container');
        $map->first()->shouldBe('JohnOk');

        $this->all()->shouldBeEqualTo($this->initializer);
        $this->lengthCheck();
    }

    function it_should_transform_items_with_callback()
    {
        $this->transform(function( & $value)
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
        $this->mergeWithKey(['name' => 'Mike'], 'wife')->get('wife.name')->shouldBe('Mike');

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
        $this->randomKey(3)->all()->shouldBeArrayAndExactLengthOf(3);

        $this->lengthCheck();
    }

    function it_should_return_random_value_from_Container()
    {
        $this->random()->shouldBeInRange($this->values()->all());

        $this->lengthCheck();
    }

    function it_should_return_array_of_exact_number_of_random_values_but_not_larger_than_Container_length()
    {
        $this->random(3)->shouldHaveCount(3);

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

    function it_should_return_new_Container_without_rejected_values_by_given_value()
    {
        $reject = $this->reject('John');

        $reject->first()->shouldBe('Doe');
        $reject->length->shouldBe(count($this->initializer) - 1);

        $this->lengthCheck();
    }

    function it_should_return_new_Container_without_rejected_values_by_given_callback()
    {
        $reject = $this->reject(function($value)
        {
            return $value == 'John';
        });

        $reject->first()->shouldBe('Doe');
        $reject->length->shouldBe(count($this->initializer) - 1);

        $this->lengthCheck();
    }

    function it_should_encrypt_items_and_should_be_exact_like_encrypted_initializer()
    {
        $key = 'cypher';
        $expires = time() + 3600;

        $encrypted = $this->encrypt($key, $expires);

        $encrypted->shouldBeString();

        $payload = [
            'exp' => $expires,
            'container' => json_encode($this->initializer, 0)
        ];

        $encrypted->shouldBeEqualTo(JWT::encode($payload, $key));
    }

    function it_should_reduce_items_to_one_value()
    {
        $initializer = [1,2,3,4,5];

        $reduce = $this->fromArray($initializer)->reduce(function($carry, $item)
        {
            return $carry += $item;
        });

        $reduce->shouldBeInteger();

        $reduce->shouldBe(15);
    }

    function it_should_decrypt_items_and_should_be_equal_to_initializer()
    {
        $key = 'cypher';
        $expires = time() + 3600;

        $encrypted = $this->encrypt($key, $expires);

        $this->fromEncrypted($encrypted, $key)->all()->shouldBeEqualTo($this->initializer);
    }

    function it_should_forget_item_by_key()
    {
        $initializer = $this->initializer;

        unset($initializer['wife']);

        $this->forget('wife')->all()->shouldBeEqualTo($initializer);

        $this->minusLengthCheck();
    }

    function it_should_forget_item_by_key_with_dot_notation_syntax()
    {
        $initializer = $this->initializer;

        unset($initializer['wife']['hobby']);

        $this->forget('wife.hobby')->all()->shouldBeEqualTo($initializer);

        $obj = new \stdClass();
        $obj->value = 15;

        $this->initializer['new'] = $obj;

        $match = $this->initializer;

        unset($match['new']->value);

        $this->fromArray($this->initializer)->forget('new.value')->all()->shouldBe($match);

        $this->lengthCheck();
    }

    function it_should_reset_Container_to_empty_array()
    {
        $reset = $this->reset()->all();

        $reset->shouldBeArray();
        $reset->shouldHaveCount(0);
    }

    function it_should_reverse_items()
    {
        $this->reverse()->all()->shouldBeEqualTo(
            array_reverse($this->initializer)
        );

        $this->lengthCheck();
    }

    function it_should_return_all_items()
    {
        $this->all()->shouldBeEqualTo($this->initializer);
    }

    function it_should_return_Container_copy()
    {
        $copy = $this->copy();

        $copy->shouldHaveType('im\Primitive\Container\Container');
        $copy->all()->shouldBeEqualTo($this->initializer);
        $copy->shouldHaveCount(count($this->initializer));

        $this->lengthCheck();
    }

    function it_should_group_items_by_similar_key()
    {
        $initializer = [
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'johndoe@example.com',
                'order' => 'first'
            ],
            [
                'name' => 'Jane',
                'surname' => 'Doe',
                'email' => 'janedoe@example.com',
                'order' => 'first'
            ],
            [
                'name' => 'John',
                'surname' => 'McDonald',
                'email' => 'johnmc@example.com',
                'order' => 'second'
            ],
            [
                'name' => 'Jane',
                'surname' => 'McDonald',
                'email' => 'janemc@example.com',
                'order' => 'second'
            ]
        ];

        $groupBy = $this->fromArray($initializer)->groupBy('order');

        $groupBy->shouldHaveType('im\Primitive\Container\Container');
        $groupBy->all()->shouldBe(
            [
                'first' => [
                    [
                        'name' => 'John',
                        'surname' => 'Doe',
                        'email' => 'johndoe@example.com',
                        'order' => 'first'
                    ],
                    [
                        'name' => 'Jane',
                        'surname' => 'Doe',
                        'email' => 'janedoe@example.com',
                        'order' => 'first'
                    ]
                ],
                'second' => [
                    [
                        'name' => 'John',
                        'surname' => 'McDonald',
                        'email' => 'johnmc@example.com',
                        'order' => 'second'
                    ],
                    [
                        'name' => 'Jane',
                        'surname' => 'McDonald',
                        'email' => 'janemc@example.com',
                        'order' => 'second'
                    ]
                ]
            ]
        );

        $this->lengthCheck();
    }

    function it_should_group_items_by_key_with_callback()
    {
        $initializer = [
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'johndoe@example.com',
                'order' => 'first'
            ],
            [
                'name' => 'Jane',
                'surname' => 'Doe',
                'email' => 'janedoe@example.com',
                'order' => 'first'
            ],
            [
                'name' => 'John',
                'surname' => 'McDonald',
                'email' => 'johnmc@example.com',
                'order' => 'second'
            ],
            [
                'name' => 'Jane',
                'surname' => 'McDonald',
                'email' => 'janemc@example.com',
                'order' => 'second'
            ]
        ];

        $groupBy = $this->fromArray($initializer)->groupBy(function($value, $key)
        {
            return $value['order'];
        });

        $groupBy->shouldHaveType('im\Primitive\Container\Container');
        $groupBy->all()->shouldBe(
            [
                'first' => [
                    [
                        'name' => 'John',
                        'surname' => 'Doe',
                        'email' => 'johndoe@example.com',
                        'order' => 'first'
                    ],
                    [
                        'name' => 'Jane',
                        'surname' => 'Doe',
                        'email' => 'janedoe@example.com',
                        'order' => 'first'
                    ]
                ],
                'second' => [
                    [
                        'name' => 'John',
                        'surname' => 'McDonald',
                        'email' => 'johnmc@example.com',
                        'order' => 'second'
                    ],
                    [
                        'name' => 'Jane',
                        'surname' => 'McDonald',
                        'email' => 'janemc@example.com',
                        'order' => 'second'
                    ]
                ]
            ]
        );
    }

    function it_should_return_new_Container_except_given_keys()
    {
        $except = $this->except(['email', 'wife']);

        $initializer = $this->initializer;

        unset($initializer['email'], $initializer['wife']);

        $except->shouldHaveType('im\Primitive\Container\Container');
        $except->all()->shouldBe($initializer);
        $except->shouldHaveCount(count($initializer));

        $this->lengthCheck();
        $this->all()->shouldBe($this->initializer);
    }

    function it_should_return_new_Container_except_given_index()
    {
        $exceptIndex = $this->exceptIndex(0);

        $initializer = $this->initializer;

        unset($initializer['name']);

        $exceptIndex->shouldHaveType('im\Primitive\Container\Container');
        $exceptIndex->all()->shouldBe($initializer);
        $exceptIndex->shouldHaveCount(count($initializer));

        $this->lengthCheck();
        $this->all()->shouldBe($this->initializer);
    }

    function it_should_throw_exception_if_given_equal_or_larger_index()
    {
        $this->shouldThrow('im\Primitive\Support\Exceptions\OffsetNotExistsException')
             ->during('exceptIndex', [count($this->initializer)]);
    }

    function it_should_return_rest_items_after_given_index()
    {
        $initializer = $this->initializer;

        unset($initializer['name'], $initializer['surname']);

        $rest = $this->restAfterIndex(1);

        $rest->all()->shouldBeEqualTo($initializer);
        $rest->shouldHaveType('im\Primitive\Container\Container');
        $rest->shouldHaveCount(count($initializer));

        $this->lengthCheck();
    }

    function it_should_return_rest_items_after_given_key()
    {
        $initializer = $this->initializer;

        unset($initializer['name'], $initializer['surname']);

        $rest = $this->restAfterKey('surname');

        $rest->all()->shouldBeEqualTo($initializer);
        $rest->shouldHaveType('im\Primitive\Container\Container');
        $rest->shouldHaveCount(count($initializer));

        $this->lengthCheck();
    }

    function it_should_flatten_items()
    {
        $match = ['John', 'Doe', 'johndoe@example.com', 'Jane', 'Doe', 'janedoe@example.com', 'music'];

        $this->flatten()->all()->shouldBeEqualTo($match);
    }

    function it_should_take_column_by_key_from_items_arrays()
    {
        $initializer = [
                        ['name' => 'John',
                         'surname' => 'Doe',
                         'email' => 'johndoe@example.com'],
                        ['name' => 'Jane',
                         'surname' => 'Doe',
                         'email' => 'janedoe@example.com',
                         'hobby' =>'music']
                       ];

        $this->fromArray($initializer);

        $column = $this->column('name');

        $column->all()->shouldBeEqualTo(['John', 'Jane']);
        $column->shouldHaveType('im\Primitive\Container\Container');
        $column->shouldHaveCount(2);

        $this->lengthCheck(count($initializer));
    }

    function it_should_leave_only_truly_items()
    {
        $initializer = [
            'name' => 'Jane',
            'surname' => 'Doe',
            'email' => 'janedoe@example.com',
            'hobby' =>'music',
            false,
            0,
            '',
            null
        ];

        $this->fromArray($initializer);

        $truly = $this->truly();
        $truly->all()->shouldBeEqualTo(['name' => 'Jane',
                                        'surname' => 'Doe',
                                        'email' => 'janedoe@example.com',
                                        'hobby' =>'music']);
        $truly->shouldHaveCount(4);

        $this->lengthCheck(count($initializer));
    }

    function it_should_take_all_items_by_key_recursively()
    {
        $take = $this->take('name');

        $take->all()->shouldBe(['John', 'Jane']);
        $take->shouldHaveCount(2);

        $this->lengthCheck();
    }

    function it_should_pull_value_and_remove_it()
    {
        $pulled = $this->pull('email');

        $pulled->shouldBe($this->initializer['email']);

        $initializer = $this->initializer;

        unset($initializer['email']);

        $this->all()->shouldBeEqualTo($initializer);

        $this->minusLengthCheck();
    }

    function it_should_pull_value_with_dot_notation_and_remove_it()
    {
        $pulled = $this->pull('wife.name');

        $pulled->shouldBe($this->initializer['wife']['name']);

        $initializer = $this->initializer;

        unset($initializer['wife']['name']);

        $this->all()->shouldBeEqualTo($initializer);

        $this->lengthCheck();
    }

    function it_should_recursively_remove_all_items_by_key_and_return_new_Container_without_this_key()
    {
        $without = $this->without('name');

        $without->shouldHaveType('im\Primitive\Container\Container');
        $without->has('name')->shouldBe(false);
        $without->has('wife.name')->shouldBe(false);
        $without->shouldHaveCount(count($this->initializer) - 1);

        $this->lengthCheck();
    }

    function it_should_intersect_items_values_with_given_array_values()
    {
        $this->fromArray(['name' => 'John',
                          'surname' => 'Doe',
                          'email' => 'johndoe@example.com']);

        $intersect = $this->intersect(['John', 'johndoe@example.com']);
        $intersect->shouldHaveType('im\Primitive\Container\Container');
        $intersect->all()->shouldBe(['name' => 'John', 'email' => 'johndoe@example.com']);

        $this->lengthCheck(3);
    }

    function it_should_intersect_items_values_with_given_array_values_with_key_check()
    {
        $with = [
            'John',
            'email' => 'johndoe@example.com'
        ];

        $this->fromArray(['name' => 'John',
                          'surname' => 'Doe',
                          'email' => 'johndoe@example.com']);

        $intersect = $this->intersect($with, true);
        $intersect->shouldHaveType('im\Primitive\Container\Container');
        $intersect->all()->shouldBe(['email' => 'johndoe@example.com']);

        $this->lengthCheck(3);
    }

    function it_should_intersect_items_keys_with_given_array_keys()
    {
        $keys = ['name' => 'foo'];

        $this->fromArray(['name' => 'John',
                          'surname' => 'Doe',
                          'email' => 'johndoe@example.com']);

        $intersectKeys = $this->intersectKey($keys);
        $intersectKeys->shouldHaveType('im\Primitive\Container\Container');
        $intersectKeys->all()->shouldBe(['name' => 'John']);

        $this->lengthCheck(3);
    }

    function it_should_sort_items_with_callable_in_Container()
    {
        $initializer = [
            [
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'johndoe@example.com',
                'order' => 1
            ],
            [
                'name' => 'Jane',
                'surname' => 'Doe',
                'email' => 'janedoe@example.com',
                'order' => 2
            ],
            [
                'name' => 'John',
                'surname' => 'McDonald',
                'email' => 'johnmc@example.com',
                'order' => 0
            ],
            [
                'name' => 'Jane',
                'surname' => 'McDonald',
                'email' => 'janemc@example.com',
                'order' => 4
            ]
        ];

        $this->fromArray($initializer);

        $this->sort(function($a, $b)
        {
            $a = $a['order'];
            $b = $b['order'];

            if ($a > $b) return 1;
            if ($a < $b) return -1;

            return 0;
        });

        $this->first()->shouldBe([
            'name' => 'John',
            'surname' => 'McDonald',
            'email' => 'johnmc@example.com',
            'order' => 0
        ]);

        $this->last()->shouldBe([
            'name' => 'Jane',
            'surname' => 'McDonald',
            'email' => 'janemc@example.com',
            'order' => 4
        ]);
    }

    function it_should_calculate_sum_of_items()
    {
        $sum = [10, 20, 1, [20, [10]]];
        $init = array_merge($this->initializer, $sum);

        $int = $this->fromArray($init)->sum();

        $int->shouldHaveType('im\Primitive\Int\Int');

        $int->value()->shouldBe(61);
    }

    function it_should_reset_keys_in_Container()
    {
        $this->resetKeys()->keys()->all()->shouldBe([0, 1, 2, 3]);
    }

    function it_should_check_if_Container_is_associative()
    {
        $this->isAssoc()->shouldBe(true);
    }

    function it_should_check_if_Container_is_not_associative()
    {
        $this->isNotAssoc()->shouldBe(false);
    }

    function it_should_check_if_Container_is_multi_dimension()
    {
        $this->isMulti()->shouldBe(true);
    }

    function it_should_check_if_Container_is_not_multi_dimension()
    {
        $this->isNotMulti()->shouldBe(false);
    }

    function it_should_check_if_Container_is_empty()
    {
        $this->isEmpty()->shouldBe(false);
    }

    function it_should_check_if_Container_is_not_empty()
    {
        $this->isNotEmpty()->shouldBe(true);
    }

    function it_should_convert_Container_to_array()
    {
        $this->fromArray([
            container(['John']), container(['Jane'])
        ]);

        $array = $this->toArray();
        $array->shouldBe([
            ['John'], ['Jane']
        ]);

        $this->lengthCheck(2);
    }

    function it_should_convert_Container_to_json()
    {
        $initializer = [
            'name' => 'Jane',
            'surname' => 'McDonald'
        ];

        $this->fromArray($initializer);

        $json = $this->toJson();
        $json->shouldBeString();
        $json->shouldBe('{"name":"Jane","surname":"McDonald"}');
    }

    function it_should_write_items_to_file()
    {
        $file = __DIR__.'/container.cdata';

        $this->toFile($file);

        $this->fromFile($file)->all()->shouldBe($this->initializer);
    }

    function it_should_construct_from_encrypted_Container()
    {
        $key = 'cypher';
        $expires = time() + 3600;

        $encrypted = $this->encrypt($key, $expires);

        $this->fromArray([]);

        $this->fromEncrypted($encrypted, $key)->all()->shouldBe($this->initializer);
    }

    function it_should_call_magic_to_string_and_return_json()
    {
        $match = $this->toJson();

        $this->__toString()->shouldBe($match);
    }

//    function it_should_clone_Container_without_references()
//    {
//        $clone = clone $this;
//
//        $clone->push(1);
//
//        $this->all()->shouldBe($this->initializer);
//        $this->lengthCheck();
//    }



    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    /**
     * Checks length equal to $this->initializer
     *
     * @param null $length
     */
    protected function lengthCheck($length = null)
    {
        if ( ! is_null($length))
        {
            $this->all()->shouldHaveCount($length);
            $this->length->shouldBe($length);
        }
        else
        {
            $this->all()->shouldHaveCount(count($this->initializer));
            $this->length->shouldBe(count($this->initializer));
        }
    }

    /**
     * Checks length equal to $this->initializer plus $add
     *
     * @param $add
     */
    protected function plusLengthCheck($add = 1)
    {
        $this->all()->shouldHaveCount(count($this->initializer) + $add);
        $this->length->shouldBe(count($this->initializer) + $add);
    }

    /**
     * Checks length equal to $this->initializer minus $add
     *
     * @param $add
     */
    protected function minusLengthCheck($add = 1)
    {
        $this->all()->shouldHaveCount(count($this->initializer) - $add);
        $this->length->shouldBe(count($this->initializer) - $add);
    }

    /**
     * Appends new matchers
     */
    public function getMatchers()
    {
        return array(

            'beInRange' => function($key, $match)
            {
                return in_array($key, $match);
            },

            'beArrayAndExactLengthOf' => function($array, $count)
            {
                return is_array($array) && $count === count($array);
            }

        );
    }
}

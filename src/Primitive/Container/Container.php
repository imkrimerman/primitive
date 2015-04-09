<?php namespace im\Primitive\Container;

use Closure;
use \Iterator;
use \BadMethodCallException;
use \InvalidArgumentException;
use \RecursiveIteratorIterator;

use JWT;
use im\Primitive\Int\Int;
use im\Primitive\String\String;
use im\Primitive\Object\Object;
use im\Primitive\Support\Arr;
use im\Primitive\Support\Str;
use im\Primitive\Support\Abstracts\ComplexType;
use im\Primitive\Support\Traits\StringCheckerTrait;
use im\Primitive\Support\Contracts\BooleanContract;
use im\Primitive\Support\Contracts\IntegerContract;
use im\Primitive\Support\Contracts\StringContract;
use im\Primitive\Support\Contracts\ContainerContract;
use im\Primitive\Support\Iterators\RecursiveContainerIterator;
use im\Primitive\Support\Exceptions\NotIsFileException;
use im\Primitive\Support\Exceptions\OffsetNotExistsException;
use im\Primitive\Container\Exceptions\ContainerException;
use im\Primitive\Container\Exceptions\BadLengthException;
use im\Primitive\Container\Exceptions\EmptyContainerException;

/**
 * Class Container
 *
 * @package im\Primitive\Container
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class Container extends ComplexType implements ContainerContract {

    use StringCheckerTrait;

    /**
     * Storing main items
     *
     * @var array
     */
    protected $items;

    /**
     * Constructor
     * Container can be constructed from array, json, serialized, Container or file that contains json or serialized
     *
     * @param array|string|ContainerContract|StringContract $from
     * @throws BadMethodCallException
     * @throws ContainerException
     */
    public function __construct($from = [])
    {
        if ($this->isArrayable($from))
        {
            $this->fromArray($this->retrieveValue($from));
        }
        elseif ($this->isStringable($from))
        {
            $this->fromString($this->getStringable($from));
        }
        else
        {
            throw new InvalidArgumentException('Bad constructor argument, expected string, array or Container');
        }
    }

    /**
     * Magic get method. For support of object style get.
     * First we try to find method with $item name and call it. Note that
     * you can only call methods without parameters. If no such method than
     * we try to find such key with dot notation.
     * Supports dot notation.
     *
     * @param mixed $item
     * @throws OffsetNotExistsException
     * @return mixed
     */
    public function __get($item)
    {
        if (method_exists($this, $item)) return $this->{$item}();

        if ($this->has($item)) return $this->get($item);

        throw new OffsetNotExistsException('Container item: ' . $item . ' not exists');
    }

    /**
     * Magic set method. For support of object style set.
     * Set $key with $value. Supports dot notation.
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function length()
    {
        return $this->measure();
    }

    /**
     * Getter. Supports dot notation.
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->items, $this->getKey($key), $default);
    }

    /**
     * Setter. Supports dot notation.
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        Arr::set($this->items, $this->getKey($key), $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->items;
    }

    /**
     * Push item in a Container.
     *
     * @param mixed $item
     * @return $this
     */
    public function push($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove last item from Container and return it.
     *
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Add item to the first index of Container.
     *
     * @param mixed $item
     * @return $this
     */
    public function prepend($item)
    {
        array_unshift($this->items, $item);

        return $this;
    }

    /**
     * Remove first item from Container and return it.
     *
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Search for specified value, return key on success, otherwise false.
     * If value is nested deeper than 1 level dot notation key will be returned.
     * You can specify strict search with 2 argument.
     *
     * @param mixed $value
     * @param bool|BooleanContract $strict
     * @return mixed
     */
    public function search($value, $strict = false)
    {
        return Arr::search($this->items, $this->getSearchable($value, $value), $this->getBoolable($strict));
    }

    /**
     * Check if Container has specified key.
     *
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $this->getKey($key));
    }

    /**
     * Checks if Container has specified value.
     *
     * @param mixed $value
     * @param null|bool|BooleanContract $strict
     * @return bool
     */
    public function hasValue($value, $strict = false)
    {
        return ! is_null(
            Arr::search($this->items, $this->getSearchable($value, $value), $this->getBoolable($strict))
        );
    }

    /**
     * Key an associative array by a field.
     *
     * @param  string|StringContract  $keyBy
     * @return static
     */
    public function keysByField($keyBy)
    {
        $byField = [];

        $keyBy = $this->getStringable($keyBy);

        foreach ($this->items as $item)
        {
            $key = _data_get($item, $keyBy);

            $byField[$key] = $item;
        }

        return new static($byField);
    }

    /**
     * Return first Container key.
     *
     * @throws ContainerException
     * @throws EmptyContainerException
     * @return mixed
     */
    public function firstKey()
    {
        if ($this->isEmpty()) throw new EmptyContainerException('Empty Container');

        return first_key($this->items);
    }

    /**
     * Return last Container key.
     *
     * @throws ContainerException
     * @throws EmptyContainerException
     * @return mixed
     */
    public function lastKey()
    {
        if ($this->isEmpty()) throw new EmptyContainerException('Empty Container');

        return last_key($this->items);
    }

    /**
     * Return first Container value.
     *
     * @return mixed
     * @throws \im\Primitive\Container\Exceptions\EmptyContainerException
     */
    public function first()
    {
        if ($this->isEmpty()) throw new EmptyContainerException('Empty Container');

        return first($this->items);
    }

    /**
     * Return first value that passes truth test.
     *
     * @param callable $function
     * @return mixed
     * @throws \im\Primitive\Container\Exceptions\EmptyContainerException
     */
    public function firstWhere(callable $function)
    {
        if ($this->isEmpty()) throw new EmptyContainerException('Empty Container');

        return Arr::first($this->items, $function);
    }

    /**
     * Return last value.
     *
     * @return mixed
     * @throws EmptyContainerException
     */
    public function last()
    {
        if ($this->isEmpty()) throw new EmptyContainerException('Empty Container');

        return last($this->items);
    }

    /**
     * Return last value that passes truth test.
     *
     * @param callable $function
     * @return mixed
     * @throws \im\Primitive\Container\Exceptions\EmptyContainerException
     */
    public function lastWhere(callable $function)
    {
        if ($this->isEmpty()) throw new EmptyContainerException('Empty Container');

        return Arr::last($this->items, $function);
    }

    /**
     * Unique Container items.
     *
     * @param bool|BooleanContract $recursive
     * @return $this
     */
    public function unique($recursive = false)
    {
        if ($this->getBoolable($recursive))
        {
            return new static($this->uniqueRecursive($this->items));
        }

        return new static(array_unique($this->items));
    }

    /**
     * Return Container keys.
     *
     * @return Container
     */
    public function keys()
    {
        return new static(array_keys($this->items));
    }

    /**
     * Return Container values
     *
     * @return Container
     */
    public function values()
    {
        return new static(array_values($this->items));
    }

    /**
     * Returns keys and values divided in new Container
     * with indexes 'keys' for keys and 'values' for values.
     *
     * @return Container
     */
    public function divide()
    {
        return new static(Arr::divide($this->items));
    }

    /**
     * Return items only with numeric keys.
     *
     * @return static
     */
    public function numericKeys()
    {
        $keys = new static;

        foreach ($this->items as $key => $value)
        {
            if (is_numeric($key)) $keys->set($key, $value);
        }

        return $keys;
    }

    /**
     * Return items only with not numeric keys.
     *
     * @return static
     */
    public function notNumericKeys()
    {
        $keys = new static;

        foreach ($this->items as $key => $value)
        {
            if ( ! is_numeric($key)) $keys->set($key, $value);
        }

        return $keys;
    }

    /**
     * Shuffle Container items.
     *
     * @return $this
     */
    public function shuffle()
    {
        shuffle($this->items);

        return $this;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @return static
     */
    public function dot()
    {
        return new static(Arr::dot($this->items));
    }

    /**
     * Return joined Container items.
     *
     * @param string|StringContract $glue
     * @return \im\Primitive\String\String
     */
    public function join($glue = '')
    {
        foreach ($copy = Arr::flatten($this->items) as $key => $object)
        {
            $copy[$key] = $this->getStringable($object);
        }

        return string(implode($this->getStringable($glue, ''), $copy));
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param mixed $key
     * @param null|mixed $glue
     * @return \im\Primitive\String\String
     */
    public function joinByKey($key, $glue = null)
    {
        return string(implode($glue, $this->lists($this->getKey($key))->all()));
    }

    /**
     * Get new Container with the values of a given key.
     * 1 Argument is key to make value from.
     * 2 Argument is key from the same Arrayable which will be the key.
     *
     * @param  mixed $valueByKey
     * @param  mixed $key
     * @return static
     */
    public function lists($valueByKey, $key = null)
    {
        return new static(Arr::pluck($this->items, $this->getKey($valueByKey), $this->getKey($key)));
    }

    /**
     * Return split Container items into chunks wrapped with new Container.
     *
     * @param int|IntegerContract  $size
     * @param bool|BooleanContract $preserveKeys
     * @return \im\Primitive\Container\Container
     * @throws \im\Primitive\Container\Exceptions\BadLengthException
     */
    public function chunk($size = 2, $preserveKeys = false)
    {
        $size = $this->getIntegerable($size);

        if ( ! is_integer($size) || $size > $this->length())
        {
            throw new BadLengthException('Chunk size is larger than container length');
        }

        $chunks = new static;

        foreach (array_chunk($this->items, $size, $this->getBoolable($preserveKeys)) as $value)
        {
            $chunks->push(new static($value));
        }

        return $chunks;
    }

    /**
     * Combine values from $array.
     * You can specify what to combine 'keys' or 'values' with the second argument.
     *
     * @param mixed $array
     * @param string|StringContract $what
     * @return static
     * @throws BadMethodCallException
     * @throws BadLengthException
     */
    public function combine($array, $what = 'keys')
    {
        $array = $this->retrieveValue($array);

        if (count($array) !== $this->length())
        {
            throw new BadLengthException('Container length should match array length.');
        }

        switch ($this->getStringable($what))
        {
            case 'keys':
                return new static(array_combine($array, $this->values()->all()));
            case 'values':
                return new static(array_combine($this->keys()->all(), $array));
            default:
                throw new BadMethodCallException('Argument 2 must be string or Stringable (keys or values)');
        }
    }

    /**
     * Return filtered Container.
     * You can specify recursive filter with the second argument.
     *
     * @param callable $function
     * @param bool|BooleanContract $recursive
     * @return Container
     */
    public function filter(callable $function, $recursive = false)
    {
        if ( ! $this->getBoolable($recursive))
        {
            return new static(array_filter($this->items, $function));
        }

        return new static($this->filterRecursive($function, $this->items));
    }

    /**
     * Flip keys with values.
     *
     * @return $this
     * @throws \im\Primitive\Container\Exceptions\ContainerException
     */
    public function flip()
    {
        if ($this->isMulti())
        {
            throw new ContainerException('Can\'t flip in multi-dimensional array.');
        }

        return new static(array_flip($this->items));
    }

    /**
     * Traverse Container items.
     *
     * @param callable $function
     * @return $this
     */
    public function each(callable $function)
    {
        array_map($function, $this->items);

        return $this;
    }

    /**
     * Run a map on each Container item.
     *
     * @param callable $function
     * @return $this
     */
    public function map(callable $function)
    {
        return new static(array_map($function, $this->items, $this->keys()->all()));
    }

    /**
     * Transform each item with the callback.
     *
     * @param callable $function
     * @return $this
     */
    public function transform(callable $function)
    {
        $this->items = array_map($function, $this->items);

        return $this;
    }

    /**
     * Apply a user function to every Container item.
     * You can specify recursive walk with the second argument.
     *
     * @param callable $callback
     * @param bool|BooleanContract $recursive
     * @param null|mixed $userdata
     * @return $this
     */
    public function walk(callable $callback, $recursive = false, $userdata = null)
    {
        $function = $this->getBoolable($recursive) ? 'array_walk_recursive' : 'array_walk';

        $function($this->items, $callback, $userdata);

        return $this;
    }

    /**
     * Merge Arrayable with Container.
     *
     * @param  mixed $items
     * @throws BadMethodCallException
     * @return $this
     */
    public function merge($items)
    {
        if ( ! $this->isArrayable($items))
        {
            throw new BadMethodCallException('Argument 1 must be array, Container or implement Arrayable interface');
        }

        return new static(array_merge($this->items, $this->retrieveValue($items)));
    }

    /**
     * Merge Arrayable with specified key in Container.
     *
     * @param mixed $items
     * @param mixed $key
     * @param null $default
     * @return $this
     * @throws InvalidArgumentException
     */
    public function mergeWithKey($items, $key, $default = null)
    {
        $key = $this->getKey($key);

        if ( ! $this->has($key)) throw new InvalidArgumentException('Key: '.$key.' not exists');

        $get = $this->get($key, $default);

        $value = array_merge($get, $this->retrieveValue($items));

        return $this->copy()->set($key, $value);
    }

    /**
     * Increase Container to the specified length with a value.
     *
     * @param int|IntegerContract $increaseSize
     * @param null|mixed $value
     * @return $this
     */
    public function increase($increaseSize = 1, $value = null)
    {
        $this->items = array_pad($this->items, $this->length() + $this->getIntegerable($increaseSize), $value);

        return $this;
    }

    /**
     * Return pseudo-random index from Container.
     *
     * @param int|IntegerContract $quantity
     * @throws BadMethodCallException
     * @return mixed
     */
    public function randomKey($quantity = 1)
    {
        $quantity = $this->getIntegerable($quantity);

        if ($this->isNotEmpty() && $this->length() >= $quantity && $quantity > 0)
        {
            $random = array_rand($this->items, $quantity);

            return is_array($random) ? new static($random) : $random;
        }

        throw new BadMethodCallException("1 Argument should be between 1 and the number of elements in the Container, got: {$quantity}");
    }

    /**
     * Return pseudo-random item from Container.
     *
     * @param int|IntegerContract $quantity
     * @return array
     */
    public function random($quantity = 1)
    {
        $quantity = $this->getIntegerable($quantity);

        $result = new static;

        while($quantity--)
        {
            $result->push($this->items[$this->randomKey()]);
        }

        return $result->length() === 1 ? $result->first() : $result;
    }

    /**
     * Cut a slice of a Container.
     * You can return result or assign to Container with the forth argument.
     *
     * @param int|IntegerContract $offset
     * @param null|int|IntegerContract $length
     * @param bool|BooleanContract $preserveKeys
     * @param bool|BooleanContract $set
     * @return static|$this
     */
    public function cut($offset, $length = null, $preserveKeys = false, $set = false)
    {
        $result = array_slice(
            $this->items,
            $this->getIntegerable($offset),
            $this->getIntegerable($length),
            $this->getBoolable($preserveKeys)
        );

        if ($this->getBoolable($set))
        {
            $this->items = empty($result) ? [] : $result;

            return $this;
        }

        return new static($result);
    }

    /**
     * Create new Container of all elements that do not pass a given truth test.
     *
     * @param callable|string|StringContract $callback
     * @return static
     */
    public function reject($callback)
    {
        if (is_callable($callback))
        {
            return $this->copy()->filter(function($item) use ($callback)
            {
                return ! $callback($item);
            });
        }

        $callback = $this->getStringable($callback);

        return $this->copy()->filter(function($item) use ($callback)
        {
            return $item != $callback;
        });
    }

    /**
     * Reduce items to one value.
     *
     * @param callable $callback
     * @param null|mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Encrypt Container items to JWT Token.
     *
     * @param string|StringContract $key
     * @param int|IntegerContract $expires
     * @return string
     */
    public function encrypt($key, $expires)
    {
        $payload = [
            'exp' => $this->getIntegerable($expires),
            'container' => $this->toJson()
        ];

        return JWT::encode($payload, $this->getStringable($key));
    }

    /**
     * Return base64 representation of Container.
     *
     * @return string
     */
    public function base64()
    {
        return base64_encode($this->toJson());
    }

    /**
     * Remove key from Container. Supports dot notation.
     *
     * @param mixed $key
     * @return $this
     */
    public function forget($key)
    {
        Arr::forget($this->items, $this->getStringable($key));

        return $this;
    }

    /**
     * Reset Container to empty array.
     *
     * @return $this
     */
    public function reset()
    {
        $this->items = [];

        return $this;
    }

    /**
     * Reverse Container items.
     *
     * @param bool|BooleanContract $preserveKeys
     * @return $this
     */
    public function reverse($preserveKeys = true)
    {
        $this->items = array_reverse($this->items, $this->getBoolable($preserveKeys));

        return $this;
    }

    /**
     * Return all items from Container. Alias for value.
     *
     * @return array
     */
    public function all()
    {
        return $this->value();
    }

    /**
     * Create copy of Container.
     *
     * @return Container
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * Group an associative array by a field or callback value.
     *
     * @param  \Closure|string|StringContract  $groupBy
     * @return static
     */
    public function groupBy($groupBy)
    {
        if ($this->isStringable($groupBy, true)) $groupBy = $this->getStringable($groupBy);

        $results = [];

        foreach ($this->items as $key => $value)
        {
            $results[$this->getGroupByKey($groupBy, $key, $value)][] = $value;
        }

        return new static($results);
    }

    /**
     * Return copy of Container except given keys.
     *
     * @param mixed $keys
     * @return static
     */
    public function except($keys)
    {
        return new static(Arr::except($this->items, $this->retrieveValue($keys)));
    }

    /**
     * Return copy of Container except given index.
     *
     * @param int|IntegerContract $nth
     * @throws OffsetNotExistsException
     * @return Container
     */
    public function exceptIndex($nth)
    {
        $nth = $this->getIntegerable($nth);

        if ($this->isEmpty() || $nth >= $this->length())
        {
            throw new OffsetNotExistsException('Offset: '. $nth .' not exist');
        }

        return $this->copy()->forget($this->keys()->get($nth));
    }

    /**
     * Return rest items after given index.
     *
     * @param int|IntegerContract $index
     * @return \im\Primitive\Container\Container
     * @throws \im\Primitive\Container\Exceptions\BadLengthException
     * @throws \im\Primitive\Container\Exceptions\ContainerException
     * @throws \im\Primitive\Support\Exceptions\OffsetNotExistsException
     */
    public function restAfterIndex($index)
    {
        $index = $this->getIntegerable($index);

        if ( ! is_numeric($index))
        {
            throw new BadMethodCallException('Argument 1: ' . $index . ' is not numeric');
        }

        $length = $this->length();

        if ($length <= $index)
        {
            throw new OffsetNotExistsException('Offset: '. $index .' not exists');
        }

        $index++;

        $keys = $this->keys()->cut($index, $length - 1)->flip();

        $values = $this->values()->cut($index, $length - 1)->all();

        return $keys->combine($values, 'values');
    }

    /**
     * Return rest items after given key.
     *
     * @param mixed $key
     * @return \im\Primitive\Container\Container
     * @throws \im\Primitive\Container\Exceptions\ContainerException
     * @throws \im\Primitive\Support\Exceptions\OffsetNotExistsException
     */
    public function restAfterKey($key)
    {
        $key = $this->getKey($key);

        if ( ! array_key_exists($key, $this->items))
        {
            throw new OffsetNotExistsException('Key: '. $key .' not exists');
        }

        $index = $this->keys()->flip()->get($key);

        return $this->restAfterIndex($index);
    }

    /**
     * Flatten Container items.
     *
     * @return $this
     */
    public function flatten()
    {
        return new static(Arr::flatten($this->items));
    }


    /**
     * Calculate difference of Container and given Arrayable.
     *
     * @param mixed $items
     * @return static
     */
    public function difference($items)
    {
        if ($this->isArrayable($items))
        {
            return new static(array_diff_key($this->items, $this->retrieveValue($items)));
        }

        throw new BadMethodCallException('Argument 1 should be array, Container or implement ArrayableContract');
    }

    /**
     * Get gathered column of a nested array element.
     *
     * @param mixed $key
     * @return static
     */
    public function column($key)
    {
        return new static(Arr::fetch($this->items, $this->getKey($key)));
    }

    /**
     * Remove all not true items from Container (null, '', false, 0, []).
     * You can specify second argument to make it recursive.
     *
     * @param bool|BooleanContract $recursive
     * @param \Closure|string|StringContract|null $function
     * @return $this
     */
    public function truly($recursive = false, $function = null)
    {
        $recursive = $this->getBoolable($recursive);

        $function = $this->getSearchable($function, null);

        if (is_null($function) || ! $function instanceof Closure)
        {
            $function = function ($item) {return ! empty($item);};
        }

        return new static($this->filter($function, $recursive));
    }

    /**
     * Take all items recursively by key.
     *
     * @param mixed $key
     * @return static
     */
    public function take($key)
    {
        $take = [];

        $key = $this->getKey($key);

        $this->walk(function ($_value_, $_key_) use ($key, & $take)
        {
            if ($_key_ == $key) $take[] = $_value_;

        }, true);

        return new static($take);
    }

    /**
     * Get a value from a Container, and remove it.
     *
     * @param mixed $key
     * @return mixed
     * @throws OffsetNotExistsException
     */
    public function pull($key)
    {
        $key = $this->getKey($key);

        if ( ! $this->has($key))
        {
            throw new OffsetNotExistsException("Key: {$key} not exists");
        }

        return Arr::pull($this->items, $key);
    }

    /**
     * Recursively remove values by key.
     *
     * @param mixed $key
     * @return static
     */
    public function without($key)
    {
        return new static($this->forgetRecursive($this->getKey($key), $this->items));
    }

    /**
     * Return intersection with Arrayable.
     * You can specify second argument to with additional index check.
     *
     * @param mixed $array
     * @param bool|BooleanContract $assoc
     * @return Container
     */
    public function intersect($array, $assoc = false)
    {
        $function = $this->getBoolable($assoc) ? 'array_intersect_assoc' : 'array_intersect';

        return new static($function($this->items, $this->retrieveValue($array)));
    }

    /**
     * Return intersection by keys with Arrayable.
     *
     * @param mixed $array
     * @return Container
     */
    public function intersectKey($array)
    {
        return new static(array_intersect_key($this->items, $this->retrieveValue($array)));
    }

    /**
     * Return user sorted Container.
     *
     * @param \Closure $function
     * @return static
     */
    public function sort(Closure $function)
    {
        $copy = $this->all();

        usort($copy, $function);

        return new static($copy);
    }

    /**
     * Reset keys to numeric.
     *
     * @return $this
     */
    public function resetKeys()
    {
        $this->items = array_values($this->items);

        return $this;
    }

    /**
     * Return sum of numeric values.
     *
     * @return \im\Primitive\Int\Int
     */
    public function sum()
    {
        return new Int(array_sum($this->flatten()->filter('is_numeric')->all()));
    }

    /**
     * Find all items by key or key value pairs.
     * You can specify second parameter to preserve keys reset.
     *
     * @param mixed $condition
     * @param bool|BooleanContract $preserveKeys
     * @throws ContainerException
     * @throws EmptyContainerException
     * @return static
     */
    public function where($condition, $preserveKeys = true)
    {
        $condition = $this->retrieveValue($condition);

        if (empty($condition)) return $this;

        return new static($this->whereCondition($condition, $this->getBoolable($preserveKeys)));
    }

    /**
     * Check if Container items is associative.
     *
     * @return bool
     */
    public function isAssoc()
    {
        return $this->keys()->filter('is_int')->length() !== $this->length();
    }

    /**
     * Check if Container items is not associative.
     *
     * @return bool
     */
    public function isNotAssoc()
    {
        return ! $this->isAssoc();
    }

    /**
     * Check if Container is multi-dimensional.
     *
     * @return bool
     */
    public function isMulti()
    {
        return $this->values()->filter('is_scalar')->length() !== $this->length();
    }

    /**
     * Check if Container is not multi-dimensional.
     *
     * @return bool
     */
    public function isNotMulti()
    {
        return ! $this->isMulti();
    }

    /**
     * Check if Container is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool) $this->length();
    }

    /**
     * Check if Container is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool) $this->length();
    }

    /**
     * Return Int Type representation of Container.
     *
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return $this->sum();
    }

    /**
     * Return Bool Type representation of Container.
     *
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return $this->toInt()->toBool();
    }

    /**
     * Return Float Type representation of Container.
     *
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return $this->toInt()->toFloat();
    }

    /**
     * Return String Type representation of Container.
     *
     * @param int $options
     *
     * @return \im\Primitive\String\String
     */
    public function toString($options = 0)
    {
        return new String($this->toJson($options));
    }

    /**
     * Return Object Type representation of Container.
     *
     * @return \im\Primitive\Object\Object
     */
    public function toObject()
    {
        return new Object($this->value());
    }

    /**
     * Construct from Json.
     *
     * @param $json
     * @return $this
     */
    public function fromJson($json)
    {
        $json = $this->getStringable($json);

        if ($this->isJson($json))
        {
            $this->initialize(json_decode($json, true));
        }

        return $this;
    }

    /**
     * Construct from file.
     * Contents can be json or serialized array.
     *
     * @param string|StringContract $file
     * @throws ContainerException
     * @throws NotIsFileException
     * @return $this
     */
    public function fromFile($file)
    {
        $file = $this->getStringable($file);

        if ( ! $this->isFile($file)) throw new NotIsFileException('Not is file: ' . $file);

        $content = file_get_contents($file);

        if ($this->isJson($content))
        {
            return $this->initialize(json_decode($content, true));
        }
        elseif ($this->isSerialized($content))
        {
            return $this->initialize(unserialize($content));
        }

        throw new ContainerException('Can\'t convert file to Container');
    }

    /**
     * Construct from serialized.
     *
     * @param string|StringContract $content
     * @return $this
     */
    public function fromSerialized($content)
    {
        $content = $this->getStringable($content);

        if ($this->isSerialized($content))
        {
            return $this->initialize(unserialize($content));
        }

        throw new BadMethodCallException('Expected serialized, got: ' . $content);
    }

    /**
     * Construct from Encrypted Container.
     *
     * @param string|StringContract $encrypted
     * @param string|StringContract $key
     * @return \im\Primitive\Container\Container
     */
    public function fromEncrypted($encrypted, $key)
    {
        $encrypted = $this->getStringable($encrypted);

        $key = $this->getStringable($key);

        if ($this->isEncryptedContainer($encrypted, $key))
        {
            $data = JWT::decode($encrypted, $key);

            return $this->fromJson($data->container);
        }

        throw new BadMethodCallException('Expected encrypted Container, got: ' . $encrypted);
    }

    /**
     * Construct from string.
     *
     * @param string|StringContract $string
     * @return $this
     * @throws \im\Primitive\Container\Exceptions\ContainerException
     * @throws \im\Primitive\Support\Exceptions\NotIsFileException
     */
    protected function fromString($string)
    {
        if ($this->isFile($string))
        {
            return $this->fromFile($string);
        }
        elseif ($this->isJson($string))
        {
            return $this->initialize(json_decode($string, true));
        }
        elseif ($this->isSerialized($string))
        {
            return $this->initialize(unserialize($string));
        }

        throw new BadMethodCallException('Argument 1 should be valid json, serialized or file with json or serialized data');
    }

    /**
     * Return json representation of Container.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Magic call method.
     * Calls native php array functions, where with key parsed or combine.
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $method = string($method);

        if ($method->startsWith('array_'))
        {
            return call_user_func_array($method(), array_merge([$this->items], $parameters));
        }

        if ($method->startsWith('where'))
        {
            $key = $method->cut(Str::length('where'), $method->length())->lower();

            return $this->where([(string) $key => $parameters[0]]);
        }

        if ($method->startsWith('combine'))
        {
            $what = $method->removeLeft('combine')->lower()->value();

            return $this->combine($parameters[0], $what);
        }

        throw new BadMethodCallException('Can\'t find method: '.$method.' in class'. __CLASS__);
    }

    /**
     *  Destructor
     */
    public function __destruct()
    {
        unset($this->items);
    }

    /**
     * Measure Container length.
     *
     * @param int $default
     * @return int
     */
    protected function measure($default = 0)
    {
        if ( ! is_array($this->items)) return $default;

        return count($this->items);
    }

    /**
     * Find where condition is array.
     *
     * @param array $conditions
     * @param bool $preserveKeys
     * @return array
     * @throws EmptyContainerException
     */
    protected function whereCondition(array $conditions, $preserveKeys)
    {
        $key = first_key($conditions);  $value = array_shift($conditions);

        $where = $this->whereRecursive($this->items, $key, $value, $preserveKeys);

        foreach ($conditions as $key => $value)
        {
            $where = $this->whereRecursive($where, $key, $value, $preserveKeys);
        }

        return $where;
    }

    /**
     * Recursively traversing tree.
     *
     * @param $array
     * @param $key
     * @param $value
     * @param $preventKeys
     * @return array
     */
    protected function whereRecursive($array, $key, $value = null, $preventKeys = false)
    {
        $outputArray = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveContainerIterator($array),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $sub)
        {
            $subIterator = $iterator->getSubIterator();

            if (isset($subIterator[$key]) && (( ! is_null($value) && $subIterator[$key] == $value ) || is_null($value)))
            {
                $outputArray += $this->iteratorToArray($iterator, $subIterator, $outputArray, $preventKeys);
            }
        }

        return $outputArray;
    }

    /**
     * Convert iterator to array.
     *
     * @param RecursiveIteratorIterator $iterator
     * @param Iterator $subIterator
     * @param array $outputArray
     * @param $preventKeys
     * @return array
     */
    protected function iteratorToArray(RecursiveIteratorIterator $iterator, Iterator $subIterator, array $outputArray, $preventKeys = false)
    {
        if ($preventKeys === false)
        {
            $key = $iterator->getSubIterator($iterator->getDepth() - 1)->key();

            $outputArray[$key] = iterator_to_array($subIterator);
        }
        else
        {
            $outputArray[] = iterator_to_array($subIterator);
        }

        return $outputArray;
    }

    /**
     * Get the "group by" key value.
     *
     * @param  \Closure|string  $groupBy
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    protected function getGroupByKey($groupBy, $key, $value)
    {
        if ( ! is_string($groupBy) && $groupBy instanceof Closure)
        {
            return $groupBy($value, $key);
        }

        return _data_get($value, $groupBy);
    }

    /**
     * Recursive filter.
     *
     * @param \Closure $function
     * @param          $items
     * @return array
     */
    protected function filterRecursive(Closure $function, $items)
    {
        if ( ! $this->isArrayable($items)) return $items;

        foreach ($items as $key => $item)
        {
            $items[$key] = $this->filterRecursive($function, $this->retrieveValue($item));
        }

        return array_filter($items, $function);
    }

    /**
     * Recursive unset.
     *
     * @param $forgetKey
     * @param mixed $items
     * @return mixed
     */
    protected function forgetRecursive($forgetKey, $items)
    {
        Arr::forget($items, $forgetKey);

        foreach ($items as $key => $item)
        {
            if ($this->isArrayable($item))
            {
                $items[$key] = $this->forgetRecursive($forgetKey, $this->retrieveValue($item));
            }
        }

        return $items;
    }

    /**
     * Unique items recursively.
     *
     * @param $items
     * @return array
     */
    protected function uniqueRecursive($items)
    {
        foreach ($items as $key => $item)
        {
            if ($this->isArrayable($item))
            {
                $this->items[$key] = $this->uniqueRecursive($this->retrieveValue($item));
            }
        }

        return array_unique($this->items);
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize($array)
    {
        $this->reset();

        foreach ((array) $array as $key => $value)
        {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function retrieveValue($value)
    {
        return $this->getArrayable($value, $this->getDefault());
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefault()
    {
        return [];
    }

    /**
     * Retrieve proper key.
     *
     * @param mixed $key
     * @param null|mixed $default
     * @return int|string
     */
    protected function getKey($key, $default = null)
    {
        return ($this->isIntegerable($key, true)) ? $this->getIntegerable($key)
                                                  : $this->getStringable($key, $default);
    }

    /*
    |--------------------------------------------------------------------------
    | JsonSerializable
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.4.0)
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by json_encode,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->filter(function($value)
        {
            return ! $value instanceof Closure;

        })->all();
    }

    /*
    |--------------------------------------------------------------------------
    | ArrayAccess
    |--------------------------------------------------------------------------
    */
    /**
     * (PHP 5 &gt;= 5.0.0)
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->items[] = $value;
        }

        $this->set($offset, $value);
    }
}

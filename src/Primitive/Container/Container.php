<?php namespace im\Primitive\Container;

use \Iterator;
use \Countable;
use \ArrayAccess;
use \JsonSerializable;
use \IteratorAggregate;
use \BadMethodCallException;
use \InvalidArgumentException;
use \RecursiveIteratorIterator;

use JWT;
use im\Primitive\Int\Int;
use im\Primitive\String\String;
use im\Primitive\Object\Object;
use im\Primitive\Support\Arr;
use im\Primitive\Support\Str;
use im\Primitive\Support\Abstracts\Type;
use im\Primitive\Support\Traits\RetrievableTrait;
use im\Primitive\Support\Traits\StringCheckerTrait;
use im\Primitive\Support\Contracts\ContainerInterface;
use im\Primitive\Support\Contracts\JsonableInterface;
use im\Primitive\Support\Contracts\FileableInterface;
use im\Primitive\Support\Contracts\ArrayableInterface;
use im\Primitive\Support\Iterators\RecursiveContainerIterator;
use im\Primitive\Support\Exceptions\NotIsFileException;
use im\Primitive\Support\Exceptions\OffsetNotExistsException;
use im\Primitive\Container\Exceptions\ContainerException;
use im\Primitive\Container\Exceptions\BadLengthException;
use im\Primitive\Container\Exceptions\EmptyContainerException;

class Container extends Type implements ContainerInterface, ArrayAccess, ArrayableInterface, JsonableInterface, JsonSerializable, FileableInterface, Countable, IteratorAggregate {

    use RetrievableTrait;
    use StringCheckerTrait;

    /*
    |--------------------------------------------------------------------------
    | Storing main items
    |--------------------------------------------------------------------------
    */
    /**
     * @var array
     */
    protected $items;

    /**
     * Constructor
     *
     * Container can be constructed from array, json, serialized, Container or file that contains json or serialized
     *
     * @param array|string|ContainerInterface|StringInterface $from
     *
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
     * Magic get method
     *
     * For support of object style get
     *
     * @param $item
     *
     * @throws OffsetNotExistsException
     * @return null
     */
    public function __get($item)
    {
        if (method_exists($this, $item))
        {
            return $this->{$item}();
        }

        if (isset($this->items[$item]))
        {
            return value($this->items[$item]);
        }

        throw new OffsetNotExistsException('Container item: ' . $item . ' not exists');
    }


    /**
     * Magic set method
     *
     * For support of object style set
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }


    /**
     * @return int
     */
    public function length()
    {
        return $this->measure();
    }


    /**
     * Getter
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return value(Arr::get($this->items, $key, $default));
    }

    /**
     * Setter
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $this->items = Arr::set($this->items, $key, $value);

        return $this;
    }

    /**
     * @return array
     */
    public function value()
    {
        return $this->items;
    }

    /**
     * Push items in the Container,
     *
     * If $key specified, $item will be pushed to specific $key.
     *
     * @param $item
     * @param null $key
     *
     * @return $this
     */
    public function push($item, $key = null)
    {
        if (empty($key))
        {
            $this->items[] = $item;

            return $this;
        }

        return $this->put($key, $item);
    }


    /**
     * Put value into Container
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function put($key, $value)
    {
        if ($this->has($key))
        {
            $this->items = Arr::set($this->items, $key, $value);
        }
        else
        {
            $this->items = Arr::add($this->items, $key, $value);
        }

        return $this;
    }

    /**
     * Remove last item from Container and returns it.
     *
     * @return mixed
     */
    public function pop()
    {
        return value(array_pop($this->items));
    }


    /**
     * Adds item to the first index of Container.
     *
     * @param $item
     *
     * @return $this
     */
    public function prepend($item)
    {
        array_unshift($this->items, $item);

        return $this;
    }


    /**
     * Removes first item from Container and returns it.
     *
     * @return mixed
     */
    public function shift()
    {
        return value(array_shift($this->items));
    }


    /**
     * Search for specified value, returns index on success, otherwise false.
     *
     * First level search.
     *
     * @param $value
     *
     * @return mixed
     */
    public function search($value)
    {
        // TODO make recursive search (return dot.notation)
        return array_search($value, $this->items);
    }


    /**
     * Check if Container has specified key
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);
    }


    /**
     * Checks if Container has specified value
     *
     * First level search
     *
     * @param      $value
     *
     * @param null $strict
     *
     * @return bool
     */
    public function hasValue($value, $strict = null)
    {
        return in_array($value, $this->items, $strict);
    }


    /**
     * Returns first Container key
     *
     * @throws ContainerException
     * @throws EmptyContainerException
     * @return mixed
     */
    public function firstKey()
    {
        if ($this->isNotEmpty())
        {
            return first_key($this->items);
        }

        throw new EmptyContainerException('Empty Container');
    }


    /**
     * Returns last Container key
     *
     * @throws ContainerException
     * @throws EmptyContainerException
     * @return mixed
     */
    public function lastKey()
    {
        if ($this->isNotEmpty())
        {
            return last_key($this->items);
        }

        throw new EmptyContainerException('Empty Container');
    }


    /**
     * Key an associative array by a field.
     *
     * @param  string|StringInterface  $keyBy
     * @return static
     */
    public function keysByField($keyBy)
    {
        // TODO make test for keysByField
        $byField = [];

        $keyBy = $this->getStringable($keyBy);

        foreach ($this->items as $item)
        {
            $key = data_get($item, $keyBy);

            $byField[$key] = $item;
        }

        return new static($byField);
    }


    /**
     * Return first Container value
     *
     * @return mixed
     * @throws \im\Primitive\Container\Exceptions\EmptyContainerException
     */
    public function first()
    {
        if ($this->isNotEmpty())
        {
            return first($this->items);
        }

        throw new EmptyContainerException('Empty Container');
    }


    /**
     * Return first value that passes truth test
     *
     * @param \Callable $function
     *
     * @return mixed
     * @throws \im\Primitive\Container\Exceptions\EmptyContainerException
     */
    public function firstWhere(callable $function)
    {
        if ($this->isNotEmpty())
        {
            return Arr::first($this->items, $function);
        }

        throw new EmptyContainerException('Empty Container');
    }


    /**
     * Return last value
     *
     * @return mixed
     * @throws EmptyContainerException
     */
    public function last()
    {
        if ($this->isNotEmpty())
        {
            return last($this->items);
        }

        throw new EmptyContainerException('Empty Container');
    }

    /**
     * Return last value that passes truth test
     *
     * @param callable $function
     *
     * @return mixed
     * @throws \im\Primitive\Container\Exceptions\EmptyContainerException
     */
    public function lastWhere(callable $function)
    {
        if ($this->isNotEmpty())
        {
            return Arr::last($this->items, $function);
        }

        throw new EmptyContainerException('Empty Container');
    }

    /**
     * Makes Container items unique
     *
     * @param bool $recursive
     *
     * @return $this
     * @throws \im\Primitive\Container\UncountableException
     */
    public function unique($recursive = false)
    {
        if ($recursive)
        {
            return new static($this->uniqueRecursive($this->items));
        }

        return new static(array_unique($this->items));
    }


    /**
     * Returns Container keys
     *
     * @return Container
     */
    public function keys()
    {
        return new static(array_keys($this->items));
    }


    /**
     * Returns Container values
     *
     * @return Container
     */
    public function values()
    {
        return new static(array_values($this->items));
    }


    /**
     * Returns keys and values divided in new Container
     * with indexes 'keys' for keys and 'values' for values
     *
     * @return Container
     */
    public function divide()
    {
        return new static(Arr::divide($this->items));
    }

    /**
     * Return items only with numeric keys
     *
     * @return static
     */
    public function numericKeys()
    {
        $keys = new static;

        foreach ($this->items as $key => $value)
        {
            if (is_numeric($key)) $keys->put($key, $value);
        }

        return $keys;
    }

    //TODO add setKey(lastKey, newKey)

    /**
     * Return items only with not numeric keys
     *
     * @return static
     */
    public function notNumericKeys()
    {
        $keys = new static;

        foreach ($this->items as $key => $value)
        {
            if ( ! is_numeric($key)) $keys->put($key, $value);
        }

        return $keys;
    }

    /**
     * Shuffles Container items
     *
     * @return $this
     */
    public function shuffle()
    {
        shuffle($this->items);

        return $this;
    }


    /**
     * Returns joined Container items with whitespace by default
     *
     * @param string $glue
     *
     * @return \im\Primitive\String\String
     */
    public function join($glue = '')
    {
        foreach ($copy = Arr::flatten($this->items) as $key => $object)
        {
            $retrieved = $this->retrieveValue($object);

            $object[$key] = is_array($retrieved) && empty($retrieved) ? '' : $retrieved;
        }

        return string(implode($glue, $copy));
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param      $key
     * @param null $glue
     *
     * @return \im\Primitive\String\String
     */
    public function joinByKey($key, $glue = null)
    {
        return string(implode($glue, $this->lists($key)->all()));
    }

    /**
     * Get new Container with the values of a given key.
     *
     * @param  string $value
     * @param  string $key
     *
     * @return static
     */
    public function lists($value, $key = null)
    {
        return new static(Arr::pluck($this->items, $value, $key));
    }

    /**
     * Return split Container items into chunks wrapped with new Container
     *
     * @param int  $size
     *
     * @param bool $preserveKeys
     *
     * @return \im\Primitive\Container\Container
     * @throws \im\Primitive\Container\Exceptions\BadLengthException
     */
    public function chunk($size = 2, $preserveKeys = false)
    {
        if ( ! is_integer($size) || $size > $this->length())
        {
            throw new BadLengthException('Chunk size is larger than container length');
        }

        $chunks = new static;

        foreach (array_chunk($this->items, $size, $preserveKeys) as $value)
        {
            $chunks->push(new static($value));
        }

        return $chunks;
    }


    /**
     * Combines values from $array
     *
     * You can specify what to combine 'keys' or 'values' with the second argument
     *
     * @param array|ContainerInterface|ArrayableInterface|stdClass $array
     * @param string $what
     *
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

        switch ($what)
        {
            case 'keys':
                return new static(array_combine($array, $this->values()->all()));
            case 'values':
                return new static(array_combine($this->keys()->all(), $array));
            default:
                throw new BadMethodCallException('Argument 2 must be string (keys or values)');
        }
    }


    /**
     * Returns filtered Container
     *
     * You can specify recursive filter with the second argument
     *
     * @param callable $function
     * @param bool $recursive
     *
     * @return Container
     */
    public function filter(callable $function, $recursive = false)
    {
        if ($recursive === false)
        {
            return new static(array_filter($this->items, $function));
        }

        return new static($this->filterRecursive($function, $this->items));
    }


    /**
     * Flips keys with values
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
     * Traverses Container items
     *
     * @param callable $function
     *
     * @return $this
     */
    public function each(callable $function)
    {
        array_map($function, $this->items);

        return $this;
    }


    /**
     * Run a map on each Container item
     *
     * @param callable $function
     *
     * @return $this
     */
    public function map(callable $function)
    {
        return new static(array_map($function, $this->items, $this->keys()->all()));
    }


    /**
     * Transform each item with the callback
     *
     * @param callable $function
     *
     * @return $this
     */
    public function transform(callable $function)
    {
        $this->items = array_map($function, $this->items);

        return $this;
    }


    /**
     * Apply a user function to every Container item
     *
     * You can specify recursive walk with the second argument
     *
     * @param callable $function
     * @param bool $recursive
     * @param null $userdata
     *
     * @return $this
     */
    public function walk(callable $function, $recursive = false, $userdata = null)
    {
        if ($recursive === false)
        {
            array_walk($this->items, $function, $userdata);
        }
        else
        {
            array_walk_recursive($this->items, $function, $userdata);
        }

        return $this;
    }


    /**
     * Merges array or Container
     *
     * You can specify exact key to merge with the second argument
     *
     * @param      $items
     * @param null $key
     *
     * @param null $default
     *
     * @throws BadMethodCallException
     * @return $this
     */
    public function merge($items, $key = null, $default = null)
    {
        if ( ! $this->isArrayable($items))
        {
            throw new BadMethodCallException('1 Argument must be array or Container');
        }

        if (is_null($key))
        {
            return new static(array_merge($this->items, $this->retrieveValue($items)));
        }
        elseif ($this->has($key))
        {
            return $this->mergeWithKey($this->retrieveValue($items), $key, $default);
        }

        throw new BadMethodCallException('Bad key given');
    }

    /**
     * Merge array with specified key in Container
     *
     * @param      $items
     * @param      $key
     * @param null $default
     *
     * @return $this
     * @throws UncountableException
     */
    public function mergeWithKey($items, $key, $default = null)
    {
        $get = $this->get($key, $default);

        $value = array_merge($get, $items);

        return $this->copy()->set($key, $value);
    }

    /**
     * Increase Container to the specified length with a value
     *
     * @param int        $increaseSize
     * @param            $value
     *
     * @return $this
     */
    public function increase($increaseSize = 1, $value = null)
    {
        $this->items = array_pad($this->items, $this->length() + $increaseSize, $value);

        return $this;
    }


    /**
     * Return pseudo-random index from Container
     *
     * @param int $quantity
     *
     * @throws BadMethodCallException
     * @return mixed
     */
    public function randomKey($quantity = 1)
    {
        if ($this->isNotEmpty() && $this->length() >= $quantity && $quantity > 0)
        {
            $random = array_rand($this->items, $quantity);

            return is_array($random) ? new static($random) : $random;
        }

        throw new BadMethodCallException("1 Argument should be between 1 and the number of elements in the Container, got: {$quantity}");
    }

    /**
     * Return pseudo-random item from Container
     *
     * @param int $quantity
     * @return array
     */
    public function random($quantity = 1)
    {
        $result = new static;

        while($quantity--)
        {
            $result->push($this->items[$this->randomKey()]);
        }

        return $result->length() === 1 ? $result->first() : $result;
    }

    /**
     * Cuts a slice of the Container
     *
     * You can return result or assign to Container with the forth argument
     *
     * @param int|IntegerInterface $offset
     * @param null|int|IntegerInterface $length
     * @param bool|BooleanInterface $preserveKeys
     * @param bool|BooleanInterface $set
     *
     * @return array|Container
     */
    public function cut($offset, $length = null, $preserveKeys = false, $set = true)
    {
        $result = array_slice(
            $this->items,
            $this->getIntegerable($offset),
            $this->getIntegerable($length),
            $this->getBoolable($preserveKeys)
        );

        if ($this->getBoolable($set) === true)
        {
            $this->items = empty($result) ? [] : $result;

            return $this;
        }

        return $result;
    }


    /**
     * Create new Container of all elements that do not pass a given truth test.
     *
     * @param $callback
     *
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

        return $this->copy()->filter(function($item) use ($callback)
        {
            return $item != $callback;
        });
    }

    /**
     * Reduce items to one value
     *
     * @param callable $callback
     * @param null     $initial
     *
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }


    /**
     * Encrypt Container items to JWT Token
     *
     * @param string|String $key
     * @param int $expires
     *
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
     * Returns base64 representation of Container
     *
     * @return string
     */
    public function base64()
    {
        return base64_encode($this->toJson());
    }

    /**
     * Remove key from Container with dot notation
     *
     * @param $key
     *
     * @return $this
     *
     */
    public function forget($key)
    {
        Arr::forget($this->items, $key);

        return $this;
    }


    /**
     * Reset Container to empty array
     *
     * @return $this
     */
    public function reset()
    {
        $this->items = [];

        return $this;
    }


    /**
     * Reverse Container items
     *
     * @param bool $preserveKeys
     *
     * @return $this
     */
    public function reverse($preserveKeys = true)
    {
        $this->items = array_reverse($this->items, $preserveKeys);

        return $this;
    }


    /**
     * Return all items from Container
     *
     * @return array
     */
    public function all()
    {
        return $this->value();
    }


    /**
     * Create copy of Container
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
     * @param  callable|string  $groupBy
     * @return static
     */
    public function groupBy($groupBy)
    {
        $results = [];

        foreach ($this->items as $key => $value)
        {
            $results[$this->getGroupByKey($groupBy, $key, $value)][] = $value;
        }

        return new static($results);
    }


    /**
     * Return copy of Container except given keys
     *
     * @param array $keys
     *
     * @return $this
     */
    public function except(array $keys)
    {
        return new static(Arr::except($this->items, $keys));
    }

    /**
     * Return copy of Container except given offset
     *
     * @param $nth
     *
     * @throws OffsetNotExistsException
     * @return Container
     */
    public function exceptIndex($nth)
    {
        if ($this->isEmpty() || (int) $nth >= $this->length())
        {
            throw new OffsetNotExistsException('Offset: '. $nth .' not exist');
        }

        return $this->copy()->forget($this->keys()->get($nth));
    }


    /**
     * Return rest items after given index
     *
     * @param int|IntegerInterface|FloatInterface $index
     *
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
     * Return rest items after given key
     *
     * @param $key
     *
     * @return \im\Primitive\Container\Container
     * @throws \im\Primitive\Container\Exceptions\ContainerException
     * @throws \im\Primitive\Support\Exceptions\OffsetNotExistsException
     */
    public function restAfterKey($key)
    {
        $key = $this->getStringable($key);

        if ( ! array_key_exists($key, $this->items))
        {
            throw new OffsetNotExistsException('Key: '. $key .' not exists');
        }

        $index = $this->keys()->flip()->get($key);

        return $this->restAfterIndex($index);
    }

    /**
     * Flatten Container items
     *
     * @return $this
     */
    public function flatten()
    {
        return new static(Arr::flatten($this->items));
    }


    /**
     * Calculate difference of Container and given Arrayable
     *
     * @param $items
     *
     * @return static
     * @throws \im\Primitive\Container\Exceptions\BadMethodCallException
     */
    public function difference($items)
    {
        if ($this->isArrayable($items))
        {
            return new static(array_diff($this->items, $this->retrieveValue($items)));
        }

        throw new BadMethodCallException('Argument 1 should be array, Container or implement ArrayableInterface');
    }


    /**
     * Get gathered column of a nested array element.
     *
     * @param $key
     * @return static
     */
    public function column($key)
    {
        return new static(Arr::fetch($this->items, $key));
    }


    /**
     * Create a new Container instance if the value isn't one already.
     *
     * @param array $data
     *
     * @return static
     */
    public function make($data = [])
    {
        if ($data instanceof Container)
        {
            return $data;
        }

        return new static($data);
    }


    /**
     * Remove all not true items from Container
     * (null, '', false, 0, [])
     *
     * You can specify second argument to make it recursive
     *
     * @param bool          $recursive
     * @param callable|null $function
     *
     * @return $this
     */
    public function truly($recursive = false, callable $function = null)
    {
        if (is_null($function))
        {
            $function = function ($item) {return ! empty($item);};
        }

        return new static($this->filter($function, $recursive));
    }


    /**
     * Take all items recursively by key
     *
     * @param $key
     *
     * @return static
     */
    public function take($key)
    {
        $take = [];

        $this->walk(function ($value, $key_) use ($key, & $take)
        {
            if ($key_ == $key) $take[] = $value;

        }, true);

        return new static($take);
    }

    /**
     * Get a value from the Container, and remove it.
     *
     * @param $key
     *
     * @return mixed
     * @throws OffsetNotExistsException
     * @throws UncountableException
     */
    public function pull($key)
    {
        if ( ! $this->has($key))
        {
            throw new OffsetNotExistsException("Key: {$key} not exists");
        }

        return Arr::pull($this->items, $key);
    }


    /**
     * Recursively removes values by key
     *
     * @param $key
     *
     * @return $this
     */
    public function without($key)
    {
        return new static($this->forgetRecursive($key, $this->items));
    }

    /**
     * Return intersection with Arrayable
     *
     * You can specify second argument to with additional index check
     *
     * @param      $array
     * @param bool $assoc
     *
     * @return Container
     */
    public function intersect($array, $assoc = false)
    {
        if ($assoc === true)
        {
            return new static(array_intersect_assoc($this->items, $this->retrieveValue($array)));
        }

        return new static(array_intersect($this->items, $this->retrieveValue($array)));
    }


    /**
     * Returns intersection by keys with Arrayable
     *
     * @param $array
     *
     * @return Container
     */
    public function intersectKey($array)
    {
        return new static(array_intersect_key($this->items, $this->retrieveValue($array)));
    }


    /**
     * User sort
     *
     * @param callable $function
     *
     * @return $this
     */
    public function sort(callable $function)
    {
        if (is_callable($function))
        {
            usort($this->items, $function);
        }

        return $this;
    }


    /**
     * Reset keys to numeric
     *
     * @return $this
     */
    public function resetKeys()
    {
        $this->items = array_values($this->items);

        return $this;
    }

    public function sum()
    {
        //TODO make test for sum
        return int(array_sum($this->filter('is_int', true)->flatten()->all()));
    }

    /**
     * Finds all items by key or key value pairs
     *
     * You can specify second parameter to preserve keys reset
     *
     * @param array $condition
     * @param bool $preserveKeys
     * @throws ContainerException
     * @throws EmptyContainerException
     *
     * @return $this
     */
    public function where(array $condition, $preserveKeys = true)
    {
        if (empty($condition))
        {
            return $this;
        }

        return new static($this->whereCondition($condition, $preserveKeys));
    }


    /**
     * Check if Container items is associative
     *
     * @return bool
     */
    public function isAssoc()
    {
        return $this->keys()->filter('is_int')->length() !== $this->length();
    }


    /**
     * Check if Container items is not associative
     *
     * @return bool
     */
    public function isNotAssoc()
    {
        return ! $this->isAssoc();
    }


    /**
     * Check if Container is multi-dimensional
     *
     * @return bool
     */
    public function isMulti()
    {
        return $this->values()->filter('is_scalar')->length() !== $this->length();
    }


    /**
     * Check if Container is not multi-dimensional
     *
     * @return bool
     */
    public function isNotMulti()
    {
        return ! $this->isMulti();
    }


    /**
     * Check if Container is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool) $this->length();
    }


    /**
     * Check if Container is not empty
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool) $this->length();
    }

    /**
     * Return Int Type representation of Container
     *
     * @return \im\Primitive\Int\Int
     */
    public function toInt()
    {
        return new Int($this->sum());
    }

    /**
     * Return Bool Type representation of Container
     *
     * @return \im\Primitive\Bool\Bool
     */
    public function toBool()
    {
        return $this->toInt()->toBool();
    }

    /**
     * Return Float Type representation of Container
     *
     * @return \im\Primitive\Float\Float
     */
    public function toFloat()
    {
        return $this->toInt()->toFloat();
    }

    /**
     * Return String Type representation of Container
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
     * Returns Object Type representation of Container
     *
     * @return \im\Primitive\Object\Object
     */
    public function toObject()
    {
        return new Object($this->value());
    }

    /**
     * Return converted Container to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($item)
        {
            return $this->retrieveValue($item);

        }, $this->items);
    }


    /**
     * Construct from array
     *
     * @param array $array
     *
     * @return $this
     */
    public function fromArray(array $array = [])
    {
        return $this->initialize($array);
    }


    /**
     * Return converted Container to Json
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->items, $options);
    }


    /**
     * Construct from Json
     *
     * @param $json
     *
     * @return $this
     */
    public function fromJson($json)
    {
        if ($this->isJson($json))
        {
            $this->initialize(json_decode($json, true));
        }

        return $this;
    }


    /**
     * Write Container items to file
     *
     * You can specify second argument to call json_encode with params
     *
     * @param $path
     * @param int $jsonOptions
     * @return bool
     */
    public function toFile($path, $jsonOptions = 0)
    {
        $source = pathinfo($path, PATHINFO_DIRNAME);

        if (is_dir($source))
        {
            return (bool) file_put_contents($path, $this->toJson($jsonOptions));
        }

        return false;
    }

    /**
     * Construct from file
     *
     * Contents can be json or serialized array
     *
     * @param string $file
     *
     * @throws ContainerException
     * @throws NotIsFileException
     * @return $this
     */
    public function fromFile($file)
    {
        if ( ! $this->isFile($file))
        {
            throw new NotIsFileException('Not is file: ' . $file);
        }

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
     * @param $content
     *
     * @return $this
     * @throws \im\Primitive\Container\Exceptions\BadMethodCallException
     * @throws \im\Primitive\Container\UncountableException
     */
    public function fromSerialized($content)
    {
        if ($this->isSerialized($content))
        {
            return $this->initialize(unserialize($content));
        }

        throw new BadMethodCallException('Expected serialized, got: ' . $content);
    }


    /**
     * Construct from Encrypted Container
     *
     * @param $encrypted
     * @param $key
     *
     * @return \im\Primitive\Container\Container
     * @throws \im\Primitive\Container\Exceptions\BadMethodCallException
     */
    public function fromEncrypted($encrypted, $key)
    {
        if ($this->isEncryptedContainer($encrypted, $key))
        {
            $data = JWT::decode($encrypted, $key);

            return $this->fromJson($data->container);
        }

        throw new BadMethodCallException('Expected encrypted Container, got: ' . $encrypted);
    }


    /**
     * Construct from string
     *
     * @param string $string
     *
     * @return $this
     * @throws \im\Primitive\Container\Exceptions\BadMethodCallException
     * @throws \im\Primitive\Container\Exceptions\ContainerException
     * @throws \im\Primitive\Container\Exceptions\NotIsFileException
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
     * Return json representation of Container
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Call magic
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
     * Measure Container length
     *
     * @param int $default
     *
     * @return int
     */
    protected function measure($default = 0)
    {
        if (is_array($this->items))
        {
            return count($this->items);
        }

        return $default;
    }


    /**
     * @param array $conditions
     * @param bool $preserveKeys
     *
     * @return array
     * @throws EmptyContainerException
     */
    protected function whereCondition(array $conditions, $preserveKeys)
    {
        $key = first_key($conditions);
        $value = array_shift($conditions);

        $where = $this->whereRecursive($this->items, $key, $value, $preserveKeys);

        foreach ($conditions as $key => $value)
        {
            $where = $this->whereRecursive($where, $key, $value, $preserveKeys);
        }

        return $where;
    }


    /**
     * Recursively traversing tree
     *
     * @param $array
     * @param $key
     * @param $value
     * @param $preventKeys
     *
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
     * @param RecursiveIteratorIterator $iterator
     * @param Iterator $subIterator
     * @param array $outputArray
     * @param $preventKeys
     *
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
     * @param  callable|string  $groupBy
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    protected function getGroupByKey($groupBy, $key, $value)
    {
        if ( ! is_string($groupBy) && is_callable($groupBy))
        {
            return $groupBy($value, $key);
        }

        return data_get($value, $groupBy);
    }


    /**
     * Recursive filter
     *
     * @param callable $function
     * @param          $items
     *
     * @return array
     */
    protected function filterRecursive(callable $function, $items)
    {
        if ( ! $this->isArrayable($items))
        {
            return $items;
        }

        foreach ($items as $key => $item)
        {
            $items[$key] = $this->filterRecursive($function, $this->retrieveValue($item));
        }

        return array_filter($items, $function);
    }


    /**
     * Recursive unset
     *
     * @param $forgetKey
     * @param $items
     *
     * @return
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
     * Unique items recursively
     *
     * @param $items
     *
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
     * Initialize items from array
     *
     * @param $array
     *
     * @return $this
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
     * Results array of items from Container or ArrayableInterface.
     *
     * @param $value
     * @return array
     */
    protected function retrieveValue($value)
    {
        return $this->getArrayable($value, $this->getDefault());
    }

    /**
     * Default value
     *
     * @return array
     */
    protected function getDefault()
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /**
     * For countable implementation
     *
     * @return int
     */
    public function count()
    {
        return $this->measure();
    }

    /*
    |--------------------------------------------------------------------------
    | JsonSerializable
    |--------------------------------------------------------------------------
    */

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->filter(function($value)
        {
            return ! is_callable($value);

        })->all();
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /**
     * @return RecursiveContainerIterator
     */
    public function getIterator()
    {
        return new RecursiveContainerIterator($this->items);
    }

    /*
    |--------------------------------------------------------------------------
    | ArrayAccess
    |--------------------------------------------------------------------------
    */

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->items[] = $value;
        }

        $this->set($offset, $value);
    }


    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }


    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }


    /**
     * @param mixed $offset
     *
     * @throws OffsetNotExistsException
     * @return null
     */
    public function offsetGet($offset)
    {
        if ($this->has($offset))
        {
            return $this->get($offset);
        }

        throw new OffsetNotExistsException('Offset: ' . $offset . ' not exists');
    }
}

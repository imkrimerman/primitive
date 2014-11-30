<?php namespace im\Primitive;

/**
 * Created by Igor Krimerman.
 * Date: 26.10.14
 * Time: 06:56
 */

use \ArrayAccess;
use im\Primitive\Exceptions\Container\BadContainerMethodArgumentException;
use im\Primitive\Exceptions\Container\BadSizeGivenException;
use im\Primitive\Exceptions\Container\NotIsFileException;
use \JsonSerializable;
use \Countable;
use \ArrayIterator;
use \IteratorAggregate;

use im\Primitive\Interfaces\ArrayableInterface;
use im\Primitive\Interfaces\JsonableInterface;
use im\Primitive\Interfaces\FileableInterface;
use im\Primitive\Interfaces\RevertableInterface;
use im\Primitive\Exceptions\Container\ContainerException;
use im\Primitive\Exceptions\Container\EmptyContainerException;
use im\Primitive\Exceptions\Container\OffsetNotExistsException;


class Container implements ArrayAccess      , ArrayableInterface , JsonableInterface, JsonSerializable,
                           FileableInterface, RevertableInterface, Countable        , IteratorAggregate
{
    /*
    |--------------------------------------------------------------------------
    | Storing clone of main items, used for reverting
    |--------------------------------------------------------------------------
    */
    private $clone;

    /*
    |--------------------------------------------------------------------------
    | Storing main items
    |--------------------------------------------------------------------------
    */
    protected $items;

    /*
    |--------------------------------------------------------------------------
    | Storing Container length
    |--------------------------------------------------------------------------
    */
    public $length;

    // --------------------------------------------------------------------------

    /**
     * Constructor
     *
     * Container can be constructed from array, json, Container or file that contains json or serialized array
     *
     * @param array|string|Container|String $from
     *
     * @throws BadContainerMethodArgumentException
     * @throws ContainerException
     */
    public function __construct($from = array())
    {
        if (is_string($from) or $from instanceof String)
        {
            if ($this->isJson($from))
            {
                $this->fromJson($from);
            }
            else
            {
                $this->fromFile($from);
            }

            return $this;
        }
        elseif ($from instanceof Container)
        {
            $from = $from->all();
        }
        elseif (! is_array($from))
        {
            throw new BadContainerMethodArgumentException('Bad argument given');
        }

        $this->items  = $from;
        $this->clone  = $from;
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

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
        if (isset($this->items[ $item ]))
        {
            return $this->items[ $item ];
        }

        throw new OffsetNotExistsException('Variable: ' . $item . ' not exists');
    }

    // ------------------------------------------------------------------------------

    /**
     * Magic set method
     *
     * For support of object style set
     *
     * @param $item
     * @param $value
     */
    public function __set($item, $value)
    {
        $this->offsetSet($item, $value);
    }

    // --------------------------------------------------------------------------

    /**
     * Pushes items in the Container,
     *
     * If $key specified, $item will be pushed to specific $key
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

            ++$this->length;

            return $this;
        }

        if ($key instanceof String)
        {
            $key = $key->__toString();
        }

        if (is_array($this->items[$key]) or $this->items[$key] instanceof Container)
        {
            $this->items[$key][] = $item;
        }
        else
        {
            $this->items[$key] = $item;
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Removes last item from Container and returns it
     *
     * @return mixed
     */
    public function pop()
    {
        $pop = array_pop($this->items);

        if ($pop !== false)
        {
            --$this->length;
        }

        return $pop;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds item to the first index of Container
     *
     * @param $item
     *
     * @return $this
     */
    public function unshift($item)
    {
        $this->length = array_unshift($this->items, $item);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Removes first item from Container and returns it
     *
     * @return mixed
     */
    public function shift()
    {
        $shift = array_shift($this->items);

        if ($shift !== false)
        {
            --$this->length;
        }

        return $shift;
    }

    // --------------------------------------------------------------------------

    /**
     * Searches for specified value, returns index on success, otherwise false
     *
     * @param $value
     *
     * @return mixed
     */
    public function find($value)
    {
        return array_search($value, $this->items);
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Container has specified values
     *
     * @param $value
     *
     * @return bool
     */
    public function has($value)
    {
        return in_array($value, $this->items);
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Container has specified key
     *
     * @param $key
     *
     * @return bool
     */
    public function hasKey($key)
    {
        return isset($this->items[ $key ]);
    }

    // --------------------------------------------------------------------------

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
            return $this->key('first');
        }

        throw new EmptyContainerException('Empty Container');
    }

    // --------------------------------------------------------------------------

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
            return $this->key('last');
        }

        throw new EmptyContainerException('Empty Container');

    }

    // --------------------------------------------------------------------------

    /**
     * Returns first Container value
     *
     * @param bool $return
     *
     * @return mixed
     */
    public function first($return = false)
    {
        if ($return === false)
        {
            $this->items = $this->items[ $this->firstKey() ];
            $this->measure();

            return $this;
        }

        return $this->items[ $this->firstKey() ];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns last Container value
     *
     * @param bool $return
     *
     * @return mixed
     */
    public function last($return = false)
    {
        if ($return === false)
        {
            $this->items = $this->items[ $this->lastKey() ];
            $this->measure();

            return $this;
        }

        return $this->items[ $this->lastKey() ];
    }

    // --------------------------------------------------------------------------

    /**
     * Makes Container items unique
     *
     * @return $this
     */
    public function unique()
    {
        $this->items = array_unique($this->items);
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns Container keys
     *
     * @return Container
     */
    public function keys()
    {
        return new Container(array_keys($this->items));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns Container values
     *
     * @return Container
     */
    public function values()
    {
        return new Container(array_values($this->items));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns keys and values divided in new Container
     * with indexes 'keys' for keys and 'values' for values
     *
     * @return Container
     */
    public function divide()
    {
        return new Container(['keys' => $this->keys(), 'values' => $this->values()]);
    }

    // --------------------------------------------------------------------------

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

    // --------------------------------------------------------------------------

    /**
     * Returns joined Container items with whitespace by default
     *
     * @param string $delimiter
     * @return string
     */
    public function implode($delimiter = ' ')
    {
        $copy = $this->copy()->flatten()->all();

        return implode($delimiter, $copy);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns split Container items into chunks wrapped with new Container
     *
     * @param int $size
     *
     * @throws BadSizeGivenException
     * @return bool|Container
     */
    public function chunk($size = 2)
    {
        if (! is_integer($size) or $size > $this->length)
        {
            throw new BadSizeGivenException('Size is larger than container length');
        }

        return new Container(array_chunk($this->items, $size));
    }

    // --------------------------------------------------------------------------

    /**
     * Combines values from $array
     *
     * You can specify what to combine 'keys' or 'values' with the second argument
     *
     * @param $array
     * @param string $what
     *
     * @throws BadContainerMethodArgumentException
     * @return array|string
     */
    public function combine($array, $what = 'keys')
    {
        switch($what)
        {
            case 'keys':
                $result = array_combine($array, $this->values()->all());
                break;
            case 'values':
                $result = array_combine($this->keys()->all(), $array);
                break;
            default:
                throw new BadContainerMethodArgumentException('Second argument must be string (keys or values)');
        }

        if (isset($result) and ! empty($result))
        {
            $this->items = $result;

        }

        unset($result);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns filtered Container
     *
     * You can specify recursive filter with the second argument
     *
     * @param callable $function
     * @param bool     $recursive
     *
     * @return Container
     */
    public function filter(callable $function, $recursive = false)
    {
        if ($recursive === false)
        {
            $new_items = array_filter($this->items, $function);
        }
        else
        {
            $new_items = $this->filterRecursive($function, $this->items);
        }

        return new Container($new_items);
    }

    // --------------------------------------------------------------------------

    /**
     * Flips Container items keys with values
     *
     * @return $this
     */
    public function flip()
    {
        $this->items = array_flip($this->items);

        return $this;
    }

    // --------------------------------------------------------------------------

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
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * Applies the callback to the Container items
     *
     * @param callable $function
     *
     * @return $this
     */
    public function map(callable $function)
    {
        $this->items = array_map($function, $this->items);
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Apply a user function to every Container item
     *
     * You can specify recursive walk with the second argument
     *
     * @param callable $function
     * @param bool     $recursive
     * @param null     $userdata
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

        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Merges array or Container
     *
     * You can specify exact key to merge with the second argument
     *
     * @param $items
     * @param null $key
     *
     * @throws BadContainerMethodArgumentException
     * @return $this
     */
    public function merge($items, $key = null)
    {
        if (! $items instanceof Container or ! is_array($items))
        {
            throw new BadContainerMethodArgumentException('$items must be array or Container');
        }

        if ($items instanceof Container)
        {
            $items = $items->all();
        }

        if ($key === null)
        {
            $this->items = array_merge($this->items, $items);
            $this->measure();
        }
        elseif ($this->hasKey($key))
        {
            if (! is_array($this->items[ $key ]))
            {
                $this->items[ $key ] = array($this->items[ $key ]);
            }

            $this->items[ $key ] = array_merge($this->items[ $key ], $items);
        }
        else
        {
            throw new BadContainerMethodArgumentException('Bad $key given');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Pad Container to the specified length with a value
     *
     * @param int $increase_size
     * @param int $value
     *
     * @return $this
     */
    public function pad($increase_size = 1, $value = 0)
    {
        $this->items = array_pad($this->items, $increase_size, $value);
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Return pseudo-random index from Container
     *
     * @param int $quantity
     *
     * @return mixed
     */
    public function rand($quantity = 1)
    {
        return array_rand($this->items, $quantity);
    }

    // --------------------------------------------------------------------------

    /**
     * Cuts a slice of the Container
     *
     * You can return result or assign to Container with the forth argument
     *
     * @param      $offset
     * @param null $length
     * @param bool $preserve_keys
     * @param bool $set
     *
     * @return array|Container
     */
    public function cut($offset, $length = null, $preserve_keys = false, $set = true)
    {
        $result = array_slice($this->items, $offset, $length, $preserve_keys);

        if ($set === true)
        {
            $this->items = $result;
            $this->measure();

            return $this;
        }

        return $result;
    }

    // --------------------------------------------------------------------------

    /**
     * Encrypts Container items and assigns to Container
     *
     * @return string
     */
    public function encrypt()
    {
        $this->items = base64_encode(gzcompress($this->flip()->toJson()));

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Decrypts Container items and assigns to Container
     *
     * @return $this
     */
    public function decrypt()
    {
        $this->fromJson(gzuncompress(base64_decode($this->items)))->flip();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Removes item from Container
     *
     * You can specify what to remove value or key
     *
     * @param $item
     * @param bool $is_value
     *
     * @return $this
     */
    public function forget($item, $is_value = false)
    {
        if ($is_value === false)
        {
            $this->forgetKey($item);
        }
        else
        {
            $this->forgetValue($item);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Saves Container state to clone, to revert in future
     *
     * @return $this
     */
    public function save()
    {
        $this->clone = $this->items;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * Reverts Container state from clone
     *
     * @return $this
     */
    public function revert()
    {
        $this->items = $this->clone;
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Resets Container to empty array
     *
     * @return $this
     */
    public function clear()
    {
        $this->items  = array();
        $this->length = 0;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Reverses Container items
     *
     * @param bool $preserve_keys
     *
     * @return $this
     */
    public function reverse($preserve_keys = true)
    {
        $this->items = array_reverse($this->items, $preserve_keys);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all items from Container
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates copy of Container
     *
     * @return Container
     */
    public function copy()
    {
        return new Container($this);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $key
     *
     * @throws OffsetNotExistsException
     * @return $this
     */
    public function take($key)
    {
        if ($this->hasKey($key))
        {
            $this->items = $this->items[$key];
            $this->measure();

            return $this;
        }

        throw new OffsetNotExistsException('Bad key:' . $key . ' given');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns copy of Container except given offset value
     *
     * @param null $nth
     *
     * @throws BadSizeGivenException
     * @throws OffsetNotExistsException
     * @return Container
     */
    public function except($nth = null)
    {
        if (is_null($nth) and $this->length > 1)
        {
            $nth = $this->length - 1;
        }
        elseif ((int)$nth >= $this->length)
        {
            throw new BadSizeGivenException('$nth: '. $nth .' is large then Container length: ' . $this->length);
        }

        if ($this->hasKey($nth))
        {
            $copy = $this->copy();
            $copy->forget($nth);

            return $copy;
        }

        throw new OffsetNotExistsException('Offset: ' . $nth . ' not exist');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns sliced copy of Container by index
     *
     * You can specify second argument to preserve keys reset
     *
     * @param $index
     *
     * @param bool $preserve_keys
     * @throws BadContainerMethodArgumentException
     * @throws EmptyContainerException
     * @return bool|Container
     */
    public function rest($index, $preserve_keys = true)
    {
        if (is_numeric($index) and $this->length > (int)$index)
        {
            $copy = $this->copy();
            $copy->cut($copy->firstKey(), (int)$index, $preserve_keys);

            return $copy;
        }

        throw new BadContainerMethodArgumentException('$index: ' . $index . ' is not numeric or larger than Container length');
    }

    // --------------------------------------------------------------------------

    /**
     * Flattens Container items
     *
     * @return $this
     */
    public function flatten()
    {
        $flattened = array();

        $this->walk(function ($value, $key) use (&$flattened)
        {
            $flattened[ $key ] = $value;

        }, true);


        $this->items = $flattened;
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Removes all not true items from Container
     * (null, '', false, 0)
     *
     * You can specify second argument to make it recursive
     *
     * @param bool $recursive
     *
     * @return $this
     */
    public function truly( $recursive = false )
    {
        $this->items = $this->filter(function ($item)
        {
            if (! empty($item) and $item !== false)
            {
                return true;
            }

            return false;

        }, $recursive);

        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Pulls all items recursively by key
     *
     * @param $key
     *
     * @return $this
     */
    public function pull($key)
    {
        $pulled = array();

        $this->walk(function ($value, $key_) use ($key, &$pulled)
        {
            if ($key_ === $key)
            {
                $pulled[] = $value;
            }

        }, true);

        $this->items = $pulled;
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Finds all items by key or key value pairs
     *
     * You can specify second parameter to preserve keys reset
     *
     * @param $condition
     * @param bool $preserve_keys
     * @throws ContainerException
     * @throws EmptyContainerException
     *
     * @return $this
     */
    public function where($condition, $preserve_keys = false)
    {
        if (empty($condition))
        {
            return $this;
        }

        if (is_array($condition))
        {
            $condition = new Container($condition);
            $neededKey = $condition->firstKey();
            $neededVal = $condition->shift();

            $where = $this->recursiveIt($this->items, $neededKey, $neededVal, $preserve_keys);

            if ($condition->isNotEmpty())
            {
                foreach ($condition as $key => $value)
                {
                    $neededKey = $condition->firstKey();
                    $neededVal = $condition->shift();

                    $where = $this->recursiveIt($where, $neededKey, $neededVal, $preserve_keys);
                }
            }

            $this->items = $where;

            unset($condition);
        }
        elseif (is_string($condition))
        {
            $this->items = $this->recursiveIt($this->items, $condition, null, $preserve_keys);
        }
        else
        {
            throw new ContainerException('$condition can be String or Array');
        }

        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Finds first item by key or key value pairs
     *
     * @param $condition
     *
     * @return mixed
     * @throws ContainerException
     */
    public function findWhere($condition)
    {
        $this->where($condition)->first();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Recursively removes values by key from Container
     *
     * @param $key
     *
     * @return $this
     */
    public function without($key)
    {
        $this->recursiveUnset($key, $this->items);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns computed intersection in new Container
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
        if ($array instanceof Container)
        {
            $array = $array->all();
        }

        if ($assoc === true)
        {
            return new Container(array_intersect_assoc($this->items, $array));
        }

        return new Container(array_intersect($this->items, $array));
    }

    // --------------------------------------------------------------------------

    /**
     * Returns computed intersection by keys in new Container
     *
     * @param $array
     *
     * @return Container
     */
    public function intersectKey($array)
    {
        if ($array instanceof Container)
        {
            $array = $array->all();
        }

        return new Container(array_intersect_key($this->items, $array));
    }

    // ------------------------------------------------------------------------------

    /**
     * User sort
     *
     * @param $function
     *
     * @return $this
     */
    public function usort($function)
    {
        if (is_string($function))
        {
            usort($this->items, $function);
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * Resets keys to numeric
     *
     * @return $this
     */
    public function lineKeys()
    {
        $this->items = array_values($this->items);

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function eq($key, $value)
    {
        if (! isset($this->items[ $key ]))
        {
            ++$this->length;
        }

        $this->items[ $key ] = $value;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * Checks if Container items is associative
     *
     * @return bool
     */
    public function isAssoc()
    {
        return $this->keys()->filter('is_int')->length !== $this->length;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if not Container items is associative
     *
     * @return bool
     */
    public function isNotAssoc()
    {
        return ! $this->isAssoc();
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Container is multi-dimensional
     *
     * @return bool
     */
    public function isMulti()
    {
        return $this->filter('is_scalar')->length !== $this->length;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Container is not multi-dimensional
     *
     * @return bool
     */
    public function isNotMulti()
    {
        return ! $this->isMulti();
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Container is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool)$this->length;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if Container is not empty
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool)$this->length;
    }

    // ------------------------------------------------------------------------------

    /**
     * Checks if Container is changed
     *
     * @return bool
     */
    public function isChanged()
    {
        return ! empty(array_diff_assoc($this->items, $this->clone));
    }

    // ------------------------------------------------------------------------------

    /**
     * Checks if Container is not changed
     *
     * @return bool
     */
    public function isNotChanged()
    {
        return empty(array_diff_assoc($this->items, $this->clone));
    }
    // --------------------------------------------------------------------------

    /**
     * Returns converted Container to array
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($item)
        {
            return $item instanceof ArrayableInterface ? $item->toArray() : $item;

        }, $this->items);
    }

    // --------------------------------------------------------------------------

    /**
     * Constructs from array
     *
     * @param array $array
     *
     * @return $this
     */
    public function fromArray(array $array = array())
    {
        $this->__construct($array);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns converted Container to Json
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->items, $options);
    }

    // --------------------------------------------------------------------------

    /**
     * Constructs from Json
     *
     * @param $json
     *
     * @return $this
     */
    public function fromJson($json)
    {
        if ($this->isJson($json))
        {
            $this->items  = json_decode($json, true);
            $this->clone  = $this->items;
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Writes Container items to file
     *
     * You can specify second argument to call json_encode with params
     *
     * @param     $path
     * @param int $json_key
     *
     * @return bool
     */
    public function toFile($path, $json_key = 0)
    {
        $source = new Container(explode(DIRECTORY_SEPARATOR, $path));
        //TODO Normal path get
        $source->pop();

        if (is_dir($source->implode('')))
        {
            return (bool)file_put_contents($path, $this->toJson($json_key));
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Constructs from file
     *
     * Contents can be json or serialized array
     *
     * @param $file
     *
     * @throws ContainerException
     * @throws NotIsFileException
     * @return $this
     */
    public function fromFile($file)
    {
        if (is_string($file) and is_file($file) and is_readable($file))
        {
            $content = file_get_contents($file);
            unset($file);

            if ($this->isJson($content))
            {
                $this->fromJson($content);
            }
            elseif ($this->isSerialized($content))
            {
                $this->items = unserialize($content);
                $this->clone = $this->items;
                $this->measure();
            }
            else
            {
                throw new ContainerException('Can\'t convert file to Container');
            }
        }
        else
        {
            throw new NotIsFileException('Not is file: ' . $file);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns json representation of Container
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    // --------------------------------------------------------------------------

    /**
     * Calls standard PHP functions
     *
     * @param $function
     * @param $args
     *
     * @return mixed
     */
    public function __call($function, $args)
    {
        if (! is_callable($function) or substr($function, 0, 6) !== 'array_')
        {
            throw new \BadMethodCallException(__CLASS__ . '->' . $function);
        }

        return call_user_func_array($function, array_merge(array($this->items), $args));
    }

    // --------------------------------------------------------------------------

    /**
     *  Destructor
     */
    public function __destruct()
    {
        unset($this->items, $this->clone, $this->length);
    }

    // --------------------------------------------------------------------------

    /**
     * Calls var_dump and exit
     *
     * Var dump
     */
    public function dump()
    {
        var_dump($this); exit;
    }

    // --------------------------------------------------------------------------

    /**
     * Measures length of Container
     *
     * @return $this
     */
    private function measure()
    {
        $this->length = count($this->items);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Joind method to get first key or last key
     *
     * @param string $what
     *
     * @throws BadContainerMethodArgumentException
     * @return mixed
     */
    private function key($what = 'first')
    {
        if ($what === 'first' or $what === 'last')
        {
            $copy = $this->copy()->all();

            if ($what === 'first')
            {
                reset($copy);
            }
            else
            {
                end($copy);
            }

            unset($what);

            return key($copy);
        }

        throw new BadContainerMethodArgumentException('Unavailable $what given (Can be passed "first" or "last")');
    }

    // --------------------------------------------------------------------------

    /**
     * Forgets by key
     *
     * @param $item
     * @return $this
     */
    private function forgetKey($item)
    {
        if ($this->hasKey($item))
        {
            unset($this->items[ $item ]);
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Forgets by value
     *
     * @param $item
     * @return $this
     */
    private function forgetValue($item)
    {
        if ($this->has($item))
        {
            $found = $this->find($item);
            unset($this->items[ $found ], $found);
            $this->measure();
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * Recursively traversing tree
     *
     * @param $array
     * @param $key
     * @param $value
     * @param $prevent_keys
     *
     * @return array
     */
    private function recursiveIt($array, $key, $value = null, $prevent_keys = false)
    {
        $outputArray = array();

        $arrIt = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array));

        foreach ($arrIt as $sub)
        {
            $subArray = $arrIt->getSubIterator();

            if ($value !== null and isset($subArray[ $key ]) and $subArray[ $key ] === $value)
            {
                if ($prevent_keys === false)
                {
                    $k = $arrIt->getSubIterator($arrIt->getDepth() - 1)->key();

                    $outputArray[ $k ] = iterator_to_array($subArray);
                }
                else
                {
                    $outputArray[] = iterator_to_array($subArray);
                }
            }
            elseif ($value === null and isset($subArray[ $key ]))
            {
                if ($prevent_keys === false)
                {
                    $k = $arrIt->getSubIterator($arrIt->getDepth() - 1)->key();

                    $outputArray[ $k ] = iterator_to_array($subArray);
                }
                else
                {
                    $outputArray[] = iterator_to_array($subArray);
                }
            }
        }

        return $outputArray;
    }

    // ------------------------------------------------------------------------------

    /**
     * Recursive filter
     *
     * @param callable $function
     * @param          $items
     *
     * @return array
     */
    private function filterRecursive(callable $function, $items)
    {
        foreach ($items as $key => & $item)
        {
            if (is_array($item))
            {
                $item = $this->filterRecursive($function, $item);
            }
            elseif($item instanceof Container)
            {
                $item = $this->filterRecursive($function, $item->all());
            }
        }

        return array_filter($items, $function);
    }

    // ------------------------------------------------------------------------------

    /**
     * Recursive unset
     *
     * @param $key
     * @param $items
     */
    private function recursiveUnset($key, & $items)
    {
        unset($items[ $key ]);

        foreach ($items as & $item)
        {
            if (is_array($item))
            {
                $this->recursiveUnset($key, $item);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if given string is Json
     *
     * @param $string
     *
     * @return bool
     */
    private function isJson($string)
    {
        if (is_string($string))
        {
            return is_array(json_decode($string, true));
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Checks if given string is serialized
     *
     * @param $string
     *
     * @return bool
     */
    private function isSerialized($string)
    {
        if ($string === 'b:0;' or @unserialize($string) !== false)
        {
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $a
     * @param $b
     */
    private function swapVars(&$a, &$b)
    {
        $a ^= $b ^= $a ^= $b;
        // list($a, $b) = array($b, $a);
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
        return $this->length;
    }

    /*
    |--------------------------------------------------------------------------
    | JsonSerializable
    |--------------------------------------------------------------------------
    */

    /**
     * @return array|string
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate
    |--------------------------------------------------------------------------
    */

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
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
        else
        {
            $this->items[ $offset ] = $value;
        }

        $this->measure();
    }

    // --------------------------------------------------------------------------

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->items[ $offset ]);
    }

    // --------------------------------------------------------------------------

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[ $offset ]);
        $this->measure();
    }

    // --------------------------------------------------------------------------

    /**
     * @param mixed $offset
     *
     * @throws OffsetNotExistsException
     * @return null
     */
    public function & offsetGet($offset)
    {
        if (isset($this->items[ $offset ]))
        {
            return $this->items[ $offset ];
        }

        throw new OffsetNotExistsException('Offset: ' . $offset . ' not exists');
    }
}

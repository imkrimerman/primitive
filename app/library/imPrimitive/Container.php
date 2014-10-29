<?php namespace im\Primitive;
/**
 * Created by PhpStorm.
 * Author: Igor Krimerman
 * Date: 26.10.14
 * Time: 06:56
 */

use \ArrayAccess;
use \JsonSerializable;
use \Countable;
use \ArrayIterator;
use \IteratorAggregate;

use im\Primitive\Interfaces\ArrayableInterface;
use im\Primitive\Interfaces\JsonableInterface;
use im\Primitive\Interfaces\FileableInterface;
use im\Primitive\Interfaces\RevertableInterface;
use im\Primitive\Exceptions\ContainerException;

use im\Primitive\String;

class Container implements ArrayAccess, ArrayableInterface, JsonableInterface, JsonSerializable, FileableInterface, RevertableInterface, Countable, IteratorAggregate
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

    /*
    |--------------------------------------------------------------------------
    | Flag to check if Container is booted and can be reverted
    |--------------------------------------------------------------------------
    */
    public $booted;

    // --------------------------------------------------------------------------

    /**
     * @param array|string|Container|String $from
     * @throws ContainerException
     */
    public function __construct( $from = array() )
    {
        if( is_string($from) or $from instanceof String )
        {
            $this->fromJson($from);

            if( $this->isEmpty() )
            {
                $container = new Container( explode(DIRECTORY_SEPARATOR, $from) );
                $container->pop();

                $dir = $container->implode( DIRECTORY_SEPARATOR );

                unset( $container );

                if( is_dir($dir) )
                {
                    $this->fromFile( $from );
                    $this->clone = $this->items;

                    return $this;
                }
                else
                {
                    $from = array();
                }
            }
        }
        elseif( $from instanceof Container )
        {
            $from = $from->all();
        }
        elseif( ! is_array($from) )
        {
            throw new ContainerException('Bad value given');
        }

        $this->items  = $from;
        $this->clone  = $from;
        $this->booted = true;
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $item
     * @param null $key
     * @return $this
     */
    public function push( $item, $key = null )
    {
        if( is_array($item) )
        {
            $this->merge( $item );
            $this->measure();
        }
        else
        {
            if( $key === null )
            {
                $this->items[] = $item;
            }
            else
            {
                $this->items[ $key ] = $item;
            }

            ++$this->length;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop( $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $item
     * @return $this
     */
    public function unshift( $item )
    {
        $this->length = array_unshift( $this->items, $item );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift( $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $value
     * @return mixed
     */
    public function find( $value )
    {
        return array_search( $value, $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $value
     * @return bool
     */
    public function has( $value )
    {
        return in_array( $value, $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $key
     * @return bool
     */
    public function hasKey( $key )
    {
        return isset( $this->items[ $key ] );
    }

    // --------------------------------------------------------------------------

    /**
     * @return mixed
     * @throws ContainerException
     */
    public function firstKey()
    {
        return $this->key('first');
    }

    // --------------------------------------------------------------------------

    /**
     * @return mixed
     * @throws ContainerException
     */
    public function lastKey()
    {
        return $this->key('last');
    }

    // --------------------------------------------------------------------------

    /**
     * @param bool $return
     * @return mixed
     */
    public function first( $return = false )
    {
        if( $return === false )
        {
            $this->items = $this->items[ $this->firstKey() ];
            $this->measure();

            return $this;
        }

        return $this->items[ $this->firstKey() ];
    }

    // --------------------------------------------------------------------------

    /**
     * @param bool $return
     * @return mixed
     */
    public function last( $return = false )
    {
        if( $return === false )
        {
            $this->items = $this->items[ $this->lastKey() ];
            $this->measure();

            return $this;
        }

        return $this->items[ $this->lastKey() ];
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function unique()
    {
        $this->items = array_unique( $this->items );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return Container
     */
    public function keys()
    {
        return new Container( array_keys($this->items) );
    }

    // --------------------------------------------------------------------------

    /**
     * @return Container
     */
    public function values()
    {
        return new Container( array_values($this->items) );
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function shuffle()
    {
        shuffle( $this->items );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $delimiter
     * @return String|string
     */
    public function implode( $delimiter = ' ' )
    {
        return implode( $delimiter, $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $size
     * @return bool|Container
     */
    public function chunk( $size = 2 )
    {
        if( ! is_integer( $size ) or $size > $this->length )
        {
            return false;
        }

        return new Container( array_chunk($this->items, $size) );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $array
     * @param string $what
     * @return array|string
     */
    public function combine( $array, $what = 'keys' )
    {
        $result = array();

        if( is_string($what) )
        {
            if( $what === 'keys' )
            {
                $result = array_combine( $array, $this->values()->all() );
            }
            elseif( $what === 'values' )
            {
                $result = array_combine( $this->keys()->all(), $array );
            }
            elseif( str_replace(' ', '', $what) === 'keys&&values' )
            {
                if( isset($array['keys']) and isset($array['values']) )
                {
                    $result = array_combine( $array['keys'], $array['values'] );
                }
            }
        }

        if( ! empty($result) )
        {
            $this->items = $result;

        }

        unset( $result );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param callable $function
     * @return Container
     */
    public function filter( callable $function )
    {
        return new Container( array_filter($this->items, $function) );
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function flip()
    {
        $this->items = array_flip( $this->items );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param callable $function
     * @return $this
     */
    public function each( callable $function )
    {
        array_map( $function, $this->items );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    public function walk( callable $function, $recursive = false, $userdata = null )
    {
        if( $recursive === false )
        {
            array_walk( $this->items, $function, $userdata );
        }
        else
        {
            array_walk_recursive( $this->items, $function, $userdata );
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param array $array
     * @param null $key
     * @return $this
     */
    public function merge( array $array, $key = null )
    {
        if( $key === null )
        {
            $this->items = array_merge( $this->items, $array );
            $this->measure();
        }
        elseif( $this->hasKey($key) )
        {
            if( ! is_array($this->items[ $key ]) )
            {
                $this->items[ $key ] = array( $this->items[ $key ] );
            }

            $this->items[ $key ] = array_merge( $this->items[ $key ], $array );
        }
        else
        {
            throw new ContainerException('Bad $key given');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $increase_size
     * @param int $value
     * @return $this
     */
    public function pad( $increase_size = 1, $value = 0 )
    {
        $this->items = array_pad( $this->items, $increase_size, $value );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $quantity
     * @return mixed
     */
    public function rand( $quantity = 1 )
    {
        return array_rand( $this->items, $quantity );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $offset
     * @param null $length
     * @param bool $set
     * @param bool $preserve_keys
     * @return array|Container
     */
    public function cut( $offset, $length = null, $set = true, $preserve_keys = false )
    {
        $result = array_slice( $this->items, $offset, $length, $preserve_keys );

        if( $set === true )
        {
            $this->items = $result;
            $this->measure();
        }

        return ( $set === true ) ? $this : $result;
    }

    // --------------------------------------------------------------------------

    /**
     * @return string
     */
    public function encrypt()
    {
        $this->items = base64_encode( gzcompress( $this->flip()->toJson() ) );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function decrypt()
    {
        $this->fromJson( gzuncompress( base64_decode( $this->items ) ) )->flip();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $key
     * @param bool $is_value
     * @return bool
     */
    public function forget( $key, $is_value = false )
    {
        if( $is_value === false )
        {
            if( $this->hasKey($key) )
            {
                unset( $this->items[ $key ] );

                return true;
            }
        }
        else
        {
            if( $this->has($key) )
            {
                $found = $this->find($key);
                unset( $this->items[ $found ], $found );

                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    public function save()
    {
        $this->clone  = $this->items;
        $this->booted = true;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function revert()
    {
        if( $this->booted )
        {
            $this->items = $this->clone;
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function clean()
    {
        $this->items  = array();
        $this->length = 0;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param bool $preserve_keys
     * @return $this
     */
    public function reverse( $preserve_keys = true )
    {
        $this->items = array_reverse( $this->items, $preserve_keys );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $what
     * @param bool $with_key
     * @return array
     * @throws ContainerException
     */
    public function pre( $what = 'first', $with_key = false )
    {
        if( $this->length or $what === 'first' or $what === 'last' )
        {
            $copy = clone $this;

            if( $what === 'last' )
            {
                $copy->reverse();
            }

            $i = 0;

            foreach( $copy as $key => $value )
            {
                if( $i++ === 1 )
                {
                    $that = ( $with_key === true ) ? array( $key => $value ) : $value;
                    unset( $copy );

                    return $that;
                }
            }
        }

        throw new ContainerException();
    }

    // --------------------------------------------------------------------------

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    // --------------------------------------------------------------------------

    /**
     * @return Container
     */
    public function copy()
    {
        return new Container( $this->all() );
    }

    // ------------------------------------------------------------------------------

    public function take( $value )
    {
        if( is_integer($value) or is_numeric($value) )
        {
            $this->items = $this->reverse()->rest( $value )->reverse();
            $this->measure();
        }
        elseif( is_string($value) )
        {
            foreach( $this->items as $key => $item )
            {
                if( $key === $value )
                {
                    $this->items = $this->items[ $value ];
                    $this->measure();

                    break;
                }
            }
        }
        else
        {
            throw new ContainerException('Bad $value given');
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param null $nth
     * @return Container
     * @throws ContainerException
     */
    public function initial( $nth = null )
    {
        if( $this->length > 1 )
        {
            if( is_null($nth) )
            {
                $nth = $this->length - 1;
            }
            elseif( (int) $nth >= $this->length )
            {
                return false;
            }

            if( $this->hasKey($nth) )
            {
                $copy = $this->copy();
                $copy->forget( $nth );

                return $copy;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $index
     * @return bool|Container
     */
    public function rest( $index )
    {
        if( is_numeric($index) and $this->length > $index )
        {
            $index = (int) $index;

            $copy = $this->copy();

            $i = 0;
            foreach( $copy as $key => $item )
            {
                if( $i++ === $index ) break;

                $copy->forget( $key );
            }

            return $copy;
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function flatten()
    {
        $flattened = array();

        $this->walk( function ( $value, $key ) use ( &$flattened )
        {
            $flattened[ $key ] = $value;
        }, true);

        $this->items = $flattened;
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function truly()
    {
        $this->items = $this->filter( function ( $item )
        {
            if( ! empty($item) and $item !== false )
            {
                return true;
            }

            return false;
        });

        $this->measure();

        return $this;
    }
    
    // --------------------------------------------------------------------------

    public function pull( $field )
    {
        $pulled = array();

        $this->walk( function ( $value, $key ) use ( $field, &$pulled )
        {
            if( $key === $field )
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
     * @param $condition
     * @return $this
     * @throws ContainerException
     */
    public function where( $condition )
    {
        $where = array();

        if( is_callable($condition) )
        {
            $where = $this->flatten()->filter( $condition );
        }
        elseif( is_array($condition) )
        {
            if( empty($condition) )
            {
                return $this;
            }

            $condition = new Container( $condition );
            $neededKey = $condition->firstKey();
            $neededVal = $condition->first( true );

            unset( $condition );

            $where = $this->recursiveIt( $this->items, $neededKey, $neededVal );
        }
        else
        {
            throw new ContainerException('$condition can be Closure or Array');
        }

        $this->items = $where;
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    public function findWhere( $key, $condition, $value )
    {
        if( is_string($condition) )
        {
            if( $condition === '==' or $condition === '===' )
            {
                foreach( $this->items as $key => $item )
                {

                }

            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    public function without( $value )
    {

    }

    // --------------------------------------------------------------------------

    public function intersect( $array, $assoc = false )
    {
        if( $array instanceof Container )
        {
            $array = $array->all();
        }

        if( $assoc === true )
        {
            return new Container( array_intersect_assoc($this->items, $array) );
        }

        return new Container( array_intersect($this->items, $array) );
    }

    // --------------------------------------------------------------------------

    public function intersectKey( $array )
    {
        if( $array instanceof Container )
        {
            $array = $array->all();
        }

        return new Container( array_intersect_key($this->items, $array) );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isAssoc()
    {
        return $this->keys()->filter('is_int')->length !== $this->length;
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isNotAssoc()
    {
        return ! $this->isAssoc();
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isMulti()
    {
        return $this->filter('is_scalar')->length !== $this->length;
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isNotMulti()
    {
        return ! $this->isMulti();
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return ! (bool) $this->length;
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool) $this->length;
    }

    // --------------------------------------------------------------------------

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    // --------------------------------------------------------------------------

    /**
     * @param array $array
     * @return $this
     */
    public function fromArray( array $array = array() )
    {
        $this->__construct( $array );
        $this->booted = true;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $options
     * @return string
     */
    public function toJson( $options = 0 )
    {
        return json_encode($this->items, $options);
    }

    // --------------------------------------------------------------------------

    /**
     * @param $json
     * @return $this
     */
    public function fromJson( $json )
    {
        if( $this->isJson($json) )
        {
            $this->items = json_decode( $json, true );
            $this->clone = $this->items;
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $path
     * @param int $json_key
     * @return bool
     */
    public function toFile( $path, $json_key = 0 )
    {
        $source = new Container( explode( DIRECTORY_SEPARATOR, $path ) );

        $source->pop();

        if( is_dir($source->implode('')) )
        {
            return (bool) file_put_contents( $path, $this->toJson($json_key) );
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $file
     * @throws ContainerException
     * @return $this
     */
    public function fromFile( $file )
    {
        if( is_string($file) and is_file($file) and is_readable($file) )
        {
            $content = file_get_contents( $file );
            unset( $file );

            if( $this->isJson($content) )
            {
                $this->fromJson( $content );
            }
            elseif( $this->is_serialized($content) )
            {
                $this->items  = unserialize( $content );
                $this->clone  = $this->items;
                $this->booted = true;
            }
            else
            {
                throw new ContainerException('Can\'t convert file to Container');
            }
        }
        else
        {
            throw new ContainerException('Not is file: ' . $file);
        }

        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->implode();
    }
    
    // --------------------------------------------------------------------------

    /**
     * @param $function
     * @param $args
     * @return mixed
     */
    public function __call( $function, $args )
    {
        if ( ! is_callable($function) or substr($function, 0, 6) !== 'array_' )
        {
            throw new \BadMethodCallException(__CLASS__.'->'.$function);
        }

        return call_user_func_array( $function, array_merge(array($this->items), $args) );
    }
    
    // --------------------------------------------------------------------------

    /**
     *  Destructor
     */
    public function __destruct()
    {
        unset( $this->items, $this->clone, $this->length, $this->booted );
    }

    // --------------------------------------------------------------------------

    /**
     * Var dump
     */
    public function dump()
    {
        var_dump( $this );
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    private function measure()
    {
        $this->length = count( $this->items );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $what
     * @return mixed
     * @throws ContainerException
     */
    private function key( $what = 'first' )
    {
        if( $what === 'first' or $what === 'last' )
        {
            $copy = $this->copy()->all();

            if( $what === 'first' )
            {
                reset($copy);
            }
            else
            {
                end($copy);
            }

            unset( $what );

            return key( $copy );
        }

        throw new ContainerException('Unavailable $what given (Can be passed "first" or "last")');
    }

    // ------------------------------------------------------------------------------

    private function recursiveIt( $array, $key, $value )
    {
        $outputArray = array();

        $arrIt = new \RecursiveIteratorIterator( new \RecursiveArrayIterator($array) );

        foreach ($arrIt as $sub)
        {
            $subArray = $arrIt->getSubIterator();

            if ( isset($subArray[ $key ]) and $subArray[ $key ] === $value )
            {
                $k = $arrIt->getSubIterator( $arrIt->getDepth() - 1 )->key();

                $outputArray[ $k ] = iterator_to_array( $subArray );
            }
        }

        return $outputArray;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $string
     * @return bool
     */
    private function isJson($string)
    {
        if( is_string($string) )
        {
            return is_array( json_decode($string, true) );
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $string
     * @return bool
     */
    private function is_serialized( $string )
    {
        if ( $string === 'b:0;' or @unserialize( $string ) !== false )
        {
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /**
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
        if( is_null($offset) )
        {
            $this->items[] = $value;
        }
        else
        {
            $this->items[ $offset ] = $value;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset( $this->items[ $offset ] );
    }

    // --------------------------------------------------------------------------

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset( $this->items[ $offset ] );
    }

    // --------------------------------------------------------------------------

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return isset( $this->items[ $offset ] ) ? $this->items[ $offset ] : null;
    }
}

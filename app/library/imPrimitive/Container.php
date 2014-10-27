<?php
/**
 * Created by PhpStorm.
 * Author: Igor Krimerman
 * Date: 26.10.14
 * Time: 06:56
 */

class Container implements ArrayAccess, ArrayInterface, JsonInterface, JsonSerializable, Countable, IteratorAggregate, Traversable{

    /*
	|--------------------------------------------------------------------------
	| Container inner variables
	|--------------------------------------------------------------------------
	*/

    private   $clone;
    protected $items;

    /*
	|--------------------------------------------------------------------------
	| Container public variables
	|--------------------------------------------------------------------------
	*/

    public $length;
    public $booted;

    // --------------------------------------------------------------------------

    /**
     * @param array $array | string json | string path_to_file
     */
    public function __construct( $array = array() )
    {
        if( ! is_array($array) and is_string($array) )
        {
            $this->fromJson($array);

            if( $this->isEmpty() )
            {
                $container = new Container( explode(DIRECTORY_SEPARATOR, $array) );
                $container->pop();

                $dir = $container->implode('');

                unset( $container );

                if( is_dir($dir) )
                {
                    $this->fromFile( $array );
                    $this->booted = true;
                }
                else
                {
                    $array = array();
                }
            }
            else
            {
                $this->booted = true;
            }
        }
        else
        {
            throw new ContainerException();
        }

        $this->items  = $array;
        $this->clone  = $array;
        $this->length = count( $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $item
     * @return $this
     */
    public function push( $item ){
        $this->items[] = $item;
        $this->length++;

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
        return in_array( $key, array_keys($this->items) );
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
     * @return mixed
     */
    public function first()
    {
        return $this->items[ $this->firstKey() ];
    }

    // --------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function last()
    {
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
     * @return array
     */
    public function keys()
    {
        return array_keys( $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @return array
     */
    public function values()
    {
        return array_values( $this->items );
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
     * @return string
     */
    public function implode( $delimiter = ' ' )
    {
        return implode( $delimiter, $this->items );
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $size
     * @return array|bool
     */
    public function chunk( $size = 2 )
    {
        if( ! is_integer( $size ) or $size > $this->length )
        {
            return false;
        }

        return array_chunk( $this->items, $size );
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
                $result = array_combine( $array, $this->values() );
            }
            elseif( $what === 'values' )
            {
                $result = array_combine( $this->keys(), $array );
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

        return $this->items;
    }

    // --------------------------------------------------------------------------

    /**
     * @param callable $function
     * @param bool $set
     * @return array|Container
     */
    public function filter( callable $function = '', $set = true )
    {
        $result = array_filter( $this->items, $function );

        if( $set === true )
        {
            $this->items = $result;
            $this->measure();
        }

        return ( $set === true ) ? $this : $result;
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
        $this->items = array_map( $function, $this->items );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param array $array
     * @return $this
     */
    public function merge( array $array )
    {
        $this->items = array_merge( $this->items, $array );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $inc_size
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
        return base64_encode( $this->toJson() );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $string
     * @return $this
     */
    public function decrypt( $string )
    {
        $this->items = base64_decode( json_encode($string, true) );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $key
     * @return bool
     */
    public function forget( $key )
    {
        if( $this->hasKey($key) )
        {
            unset( $this->items[ $key ] );

            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
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
            $copy = ( $what === 'last' ) ? array_reverse( $this->items, true ) : $this->items;
            $i    = 0;

            foreach( $copy as $key => $value )
            {
                if( $i === 1 )
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

    public function all()
    {
        return $this->toArray();
    }

    // --------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (bool) empty( $this->items );
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
            $this->items  = json_decode( $json, true );
            $this->booted = true;
            $this->measure();
        }
        else
        {
            $this->__construct();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $path
     * @param int $json_key
     * @return bool
     */
    public function toFile( $path, $json_key = JSON_PRETTY_PRINT )
    {
        $source = new Container( explode( DIRECTORY_SEPARATOR, $path ) );

        $source->pop();

        $dir = $source->implode('');

        if( is_dir($dir) )
        {
            return (bool) file_put_contents( $path, $this->toJson($json_key) );
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $file
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
                $this->booted = true;
            }
            elseif( $this->is_serialized($content) )
            {
                $this->items = unserialize( $content );
                $this->booted = true;
            }
            else
            {
                $this->__construct();
            }
        }
        else
        {
            $this->__construct();
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
     *  Destructor
     */
    public function __destruct()
    {
        unset( $this->items, $this->clone, $this->length, $this->booted );
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
        $function = 'reset';

        if( $what === 'last' )
        {
            $function = 'last';
        }
        else
        {
            throw new ContainerException();
        }

        unset( $what );

        $copy = $this->items;
        call_user_func( $function, $copy );

        $key = key( $copy );
        unset( $copy );

        return $key;
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
            $decoded = json_decode($string);

            if( is_object($decoded) or is_array($decoded) )
            {
                return true;
            }
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
    public function count(){
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

// --------------------------------------------------------------------------

    /**
     * @param $array
     * @return Container
     */
    function a( $array )
{
    return new Container( $array );
}

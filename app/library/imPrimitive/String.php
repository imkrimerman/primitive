<?php namespace im\Primitive;

/**
 * Created by Igor Krimerman.
 * Date: 26.10.14
 * Time: 13:38
 */

use \Countable;
use im\Primitive\Container;
use im\Primitive\Exceptions\StringException;

class String implements Countable
{

    private $clone;
    protected $string;
    public $length;

    // --------------------------------------------------------------------------

    /**
     * @param string $string
     */
    public function __construct( $string = '' )
    {
        if( is_string( $string ) )
        {
            $this->string = $string;
        }
        else
        {
            $this->string = '';
        }

        $this->clone = $this->string;
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $string
     * @param string $delimiter
     * @return $this
     */
    public function append( $string, $delimiter = ' ' )
    {
        if( $string instanceof String )
        {
            $string = $string->__toString();
        }

        if( is_string( $string ) and is_string( $delimiter ) )
        {
            $this->string .= "{$delimiter}{$string}";
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $string
     * @param string $delimiter
     * @return $this
     */
    public function prepand( $string, $delimiter = ' ' )
    {
        if( $string instanceof String )
        {
            $string = $string->__toString();
        }

        if( is_string( $string ) and is_string( $delimiter ) )
        {
            $this->string = "{$string}{$delimiter}{$this->string}";
            $this->measure();
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    public function eq( $string )
    {
        if( is_string( $string ) )
        {
            $this->string = $string;
            $this->clone = $string;
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param null $what
     * @return $this
     */
    public function lower( $what = null )
    {
        if( $what === 'first' )
        {
            $this->string = lcfirst( $this->string );
        }
        else
        {
            $this->string = mb_strtolower( $this->string );
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param null $what
     * @return $this
     */
    public function upper( $what = null )
    {
        if( $what === 'first' )
        {
            $this->string = ucfirst( $this->string );
        }
        elseif( $what === 'words' )
        {
            $this->string = ucwords( $this->string );
        }
        else
        {
            $this->string = mb_strtoupper( $this->string );
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function camel()
    {
        return $this->studly()->lower( 'first' );
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function dashed()
    {
        $this->string = preg_replace( '/([a-zA-Z])(?=[A-Z])/', '$1-', $this->string );
        $this->lower();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $delimiter
     * @return $this
     */
    public function snake( $delimiter = '_' )
    {
        if( ctype_lower( $this->string ) )
        {
            return $this;
        }

        $replace = '$1' . $delimiter . '$2';
        $this->string = preg_replace( '/(.)([A-Z])/', $replace, $this->string );
        $this->lower();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function studly()
    {
        $this->string = ucwords( str_replace( array( '-', '_' ), ' ', $this->string ) );
        $this->string = str_replace( ' ', '', $this->string );

        return $this;
    }

    // --------------------------------------------------------------------------

    public function find( $string )
    {

    }

    // --------------------------------------------------------------------------

    /**
     * @param $string
     * @param bool $caseSensitive
     * @return bool
     */
    public function has( $string, $caseSensitive = true )
    {
        if( $caseSensitive === true )
        {
            return strpos( $this->string, $string ) !== false;
        }
        else
        {
            return stripos( $this->string, $string ) !== false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @param $search
     * @param $replace
     * @return $this
     */
    public function replace( $search, $replace )
    {
        if( is_string( $replace ) and ( is_string( $search ) or is_array( $search ) or $search instanceof Container ) )
        {
            if( $search instanceof Container )
            {
                $search = $search->all();
            }

            $this->string = str_replace( $search, $replace, $this->string );
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $needles
     * @return bool
     */
    public function startsWith( $needles )
    {
        foreach( (array)$needles as $needle )
        {
            if( $needle != '' and strpos( $this->string, $needle ) === 0 )
            {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $needles
     * @return bool
     */
    public function endsWith( $needles )
    {
        foreach( (array)$needles as $needle )
        {
            if( (string)$needle === substr( $this->string, -strlen( $needle ) ) )
            {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $delimiter
     * @return array|Container
     */
    public function explode( $delimiter )
    {
        if( class_exists( 'Container' ) )
        {
            return new Container( explode( $delimiter, $this->string ) );
        }

        return explode( $delimiter, $this->string );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $delimiter
     * @param array $array
     * @throws StringException
     * @return $this
     */
    public function implode( $delimiter, $array )
    {
        if( $array instanceof Container )
        {
            $array = $array->all();
        }
        elseif( !is_array( $array ) )
        {
            throw new StringException( 'Unavailable $array is given' );
        }

        $this->string = implode( $delimiter, $array );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param null $what
     * @return $this
     */
    public function trim( $what = null )
    {
        if( $what === 'front' )
        {
            $this->string = ltrim( $this->string );
        }
        elseif( $what === 'back' )
        {
            $this->string = rtrim( $this->string );
        }
        elseif( $what === 'all' )
        {
            $this->replace(' ', '');
        }
        else
        {
            $this->string = trim( $this->string );
        }

        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $quantity
     * @return $this
     */
    public function repeat( $quantity = 2 )
    {
        $this->string = str_repeat( $this->string, (int)$quantity );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function shuffle()
    {
        $this->string = str_shuffle( $this->string );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param int $length
     * @return array|Container
     */
    public function split( $length = 1 )
    {
        if( class_exists( 'Container' ) )
        {
            return new Container( str_split( $this->string, $length ) );
        }

        return str_split( $this->string, $length );
    }

    // --------------------------------------------------------------------------

    /**
     * @return Container|mixed
     */
    public function wordSplit()
    {
        if( class_exists( 'Container' ) )
        {
            return new Container( str_word_count( $this->string, 2 ) );
        }

        return str_word_count( $this->string, 2 );
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function strip()
    {
        $this->string = strip_tags( $this->string );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function base64()
    {
        $this->string = base64_encode( $this->string );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function unbase64()
    {
        $this->string = base64_decode( $this->string );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param int $flags
     * @param string $encoding
     * @return $this
     */
    public function toEntities( $flags = ENT_QUOTES, $encoding = 'UTF-8' )
    {
        $this->string = htmlentities( $this->string, $flags, $encoding );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param int $flags
     * @param string $encoding
     * @return $this
     */
    public function fromEntities( $flags = ENT_QUOTES, $encoding = 'UTF-8' )
    {
        $this->string = html_entity_decode( $this->string, $flags, $encoding );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param bool $return
     * @return string
     */
    public function md5( $return = false )
    {
        if( $return === false )
        {
            return md5( $this->string );
        }

        $this->string = md5( $this->string );
        $this->measure();

        return $this;

    }

    // --------------------------------------------------------------------------

    /**
     * Echo string
     * @param string $before
     * @param string $after
     * @return $this
     */
    public function say( $before = '', $after = '' )
    {
        if( !is_string( $before ) )
        {
            $before = '';
        }

        if( !is_string( $after ) )
        {
            $after = '';
        }

        echo $before, $this->string, $after;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $offset
     * @param $length
     * @param string $encoding
     * @return $this
     */
    public function cut( $offset, $length, $encoding = 'UTF-8' )
    {
        $this->string = mb_substr( $this->string, $offset, $length, $encoding );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param int $limit
     * @param string $end
     * @return $this
     */
    public function limit( $limit = 100, $end = '...' )
    {
        if( $this->length <= $limit )
        {
            return $this;
        }

        $this->cut( 0, $limit, 'UTF-8' )->trim( 'back' )->append( $end );

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return array
     */
    public function toVars()
    {
        $vars = array();
        parse_str( $this->string, $vars );

        return $vars;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function clean()
    {
        $this->strip()->toEntities()->trim();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param int $decimals
     * @param string $decimal_delimiter
     * @param string $thousands_delimiter
     * @return $this
     */
    public function float( $decimals = 2, $decimal_delimiter = '.', $thousands_delimiter = ' ' )
    {
        if( is_numeric( $this->string ) )
        {
            $this->string = number_format( (float)$this->string, $decimals, $decimal_delimiter, $thousands_delimiter );
            $this->measure();
        }

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function compress()
    {
        $this->string = gzcompress( $this->string );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function uncompress()
    {
        $this->string = gzuncompress( $this->string );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function encrypt()
    {
        $this->compress()->base64();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function decrypt()
    {
        $this->unbase64()->uncompress();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function save()
    {
        $this->clone = $this->string;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function revert()
    {
        $this->string = $this->clone;
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param null $string
     * @return int|String
     */
    private function measure( $string = null )
    {
        if( $string === null )
        {
            $this->length = mb_strlen( $this->string );
        }

        return ( $string === null ) ? $this : mb_strlen( $string );
    }

    // ------------------------------------------------------------------------------

    public function all()
    {
        return $this->string;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !(bool)$this->length;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return (bool)$this->length;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param $string
     * @return bool
     */
    public function isJson( $string = '' )
    {
        if( empty($string) )
        {
            $string = $this->string;
        }

        if( is_string($string) )
        {
            return is_array( json_decode($string, true) );
        }

        return false;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }

    /*
    |--------------------------------------------------------------------------
    | Countable
    |--------------------------------------------------------------------------
    */

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->length;
    }
}

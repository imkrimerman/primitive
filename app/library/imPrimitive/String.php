<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 26.10.14
 * Time: 13:38
 */

class String implements Countable{

    protected $string;
    public    $length;
    
    // --------------------------------------------------------------------------

    /**
     * @param string $string
     */
    public function __construct( $string = '' )
    {
        if( is_string($string) )
        {
            $this->string = $string;
        }
        else
        {
            $this->string = '';
        }

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
        if( is_string($string) and is_string($delimiter) )
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
        if( is_string($string) and is_string($delimiter) )
        {
            $this->string = "{$string}{$delimiter}{$this->string}";
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
        return $this->studly()->lower('first');
    }

    // --------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function dashed()
    {
        $this->string = preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $this->string);
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
        if ( ctype_lower($this->string) )
        {
            return $this;
        }

        $replace      = '$1'.$delimiter.'$2';
        $this->string = preg_replace('/(.)([A-Z])/', $replace, $this->string);
        $this->lower();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function studly()
    {
        $this->string = ucwords(str_replace(array('-', '_'), ' ', $this->string));
        $this->string = str_replace(' ', '', $this->string);

        return $this;
    }

    // --------------------------------------------------------------------------

    public function find( $string )
    {

    }

    // --------------------------------------------------------------------------

    /**
     * @param $string
     * @return bool
     */
    public function has( $string )
    {
        return strpos( $this->string, $string ) !== false;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $needle
     * @param $replacement
     * @return $this
     */
    public function replace( $needle, $replacement )
    {
        if( is_string($needle) and (is_string($replacement) or is_array($replacement) or $replacement instanceof Container) )
        {
            if( $replacement instanceof Container )
            {
                $replacement = $replacement->all();
            }

            $this->string = str_replace( $replacement, $needle, $this->string );
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param $sub
     * @return bool
     */
    public function beginsWith( $sub )
    {
        return ( substr( $this->string, 0, $this->measure( $sub ) ) == $sub );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $sub
     * @return bool
     */
    public function endsWith( $sub )
    {
        return ( substr( $this->string, $this->length - $this->measure( $sub ) ) == $sub );
    }

    // --------------------------------------------------------------------------

    /**
     * @param $delimiter
     * @return array|Container
     */
    public function explode( $delimiter )
    {
        if( class_exists('Container') )
        {
            return new Container( explode($delimiter, $this->string) );
        }

        return explode($delimiter, $this->string);
    }

    // --------------------------------------------------------------------------

    /**
     * @param $delimiter
     * @param array $array
     * @return $this
     */
    public function implode( $delimiter, array $array )
    {
        if( class_exists('Container') )
        {
            //TODO Think about implementation
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
        $this->string = str_repeat( $this->string, (int) $quantity );
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
        if( class_exists('Container') )
        {
            return new Container( str_split($this->string, $length) );
        }

        return str_split( $this->string, $length );
    }
    
    // --------------------------------------------------------------------------

    /**
     * @return Container|mixed
     */
    public function wordSplit()
    {
        if( class_exists('Container') )
        {
            return new Container( str_word_count($this->string, 2) );
        }

        return str_word_count($this->string, 2);
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
    public function inBase64()
    {
        $this->string = base64_encode( $this->string );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param null $base_64_encoded
     * @return $this
     */
    public function fromBase64( $base_64_encoded = null )
    {
        if( $base_64_encoded !== null and is_string($base_64_encoded) )
        {
            $this->__construct( $base_64_encoded );
        }

        $decoded = base64_decode($this->string);

        if( $decoded !== false )
        {
            $this->string = $decoded;
            $this->measure();
        }

        unset( $decoded );

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
     * @param null $html_entities_string
     * @param int $flags
     * @param string $encoding
     * @return $this
     */
    public function fromEntities( $html_entities_string = null, $flags = ENT_QUOTES, $encoding = 'UTF-8' )
    {
        if( $html_entities_string !== null and is_string($html_entities_string) )
        {
            $this->__construct( $html_entities_string );
        }

        $this->string = html_entity_decode( $this->string, $flags, $encoding );
        $this->measure();

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function md5()
    {
        return md5( $this->string );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return $this
     */
    public function toMd5()
    {
        $this->string = $this->md5();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Echo string
     */
    public function say()
    {
        echo $this->string;
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
    public function numFormat( $decimals = 2, $decimal_delimiter = '.', $thousands_delimiter = ' ' )
    {
        if( is_numeric($this->string) )
        {
            $this->string = number_format( (float) $this->string, $decimals, $decimal_delimiter, $thousands_delimiter );
            $this->measure();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * @param null $string
     * @return int|String
     */
    private function measure( $string = null )
    {
        if( $string === null )
        {
            $this->length = strlen( $this->string );
        }

        return ( $string === null ) ? $this : strlen($string);
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

// --------------------------------------------------------------------------

/**
 * @param $string
 * @return String
 */
function s( $string )
{
    return new String($string);
}

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

    public function lower()
    {
        
    }

    // --------------------------------------------------------------------------

    public function upper()
    {

    }

    // --------------------------------------------------------------------------

    public function camel()
    {

    }

    // --------------------------------------------------------------------------

    public function dashed()
    {

    }

    // --------------------------------------------------------------------------

    public function snake()
    {

    }

    // --------------------------------------------------------------------------

    public function find( $string )
    {

    }

    // --------------------------------------------------------------------------

    public function has( $string )
    {

    }

    // --------------------------------------------------------------------------

    public function replace( $needle, $replacement )
    {

    }

    // --------------------------------------------------------------------------

    public function beginsWith( $sub )
    {
        return ( substr( $this->string, 0, $this->measure( $sub ) ) == $sub );
    }

    // --------------------------------------------------------------------------

    public function endsWith( $sub )
    {
        return ( substr( $this->string, $this->length - $this->measure( $sub ) ) == $sub );
    }

    // --------------------------------------------------------------------------

    public function explode( $delimiter )
    {
        if( class_exists('Container') )
        {
            return new Container( explode($delimiter, $this->string) );
        }

        return explode($delimiter, $this->string);
    }

    // --------------------------------------------------------------------------

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

    public function repeat( $quantity = 2 )
    {
        $this->string = str_repeat( $this->string, (int) $quantity );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    public function shuffle()
    {
        $this->string = str_shuffle( $this->string );

        return $this;
    }

    // --------------------------------------------------------------------------

    public function split( $length = 1 )
    {
        if( class_exists('Container') )
        {
            return new Container( str_split($this->string, $length) );
        }

        return str_split( $this->string, $length );
    }
    
    // --------------------------------------------------------------------------

    public function wordSplit()
    {
        if( class_exists('Container') )
        {
            return new Container( str_word_count($this->string, 2) );
        }

        return str_word_count($this->string, 2);
    }

    // --------------------------------------------------------------------------

    public function strip()
    {
        $this->string = strip_tags( $this->string );
        $this->measure();

        return $this;
    }

    // --------------------------------------------------------------------------

    public function say()
    {
        echo $this->string;
    }
    
    // --------------------------------------------------------------------------

    public function toVars()
    {
        $vars = array();
        parse_str( $this->string, $vars );
        
        return $vars;
    }

    // --------------------------------------------------------------------------

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

    public function count(){
        return $this->length;
    }

}

// --------------------------------------------------------------------------

function s( $string )
{
    return new String($string);
}

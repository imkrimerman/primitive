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

        $this->length = strlen( $this->string );

        return $this;
    }

    // --------------------------------------------------------------------------

    public function add( $string ){

    }

    // --------------------------------------------------------------------------

    public function count(){
        return $this->length;
    }
}

// --------------------------------------------------------------------------

function s( $string )
{
    return new String($string);
}
<?php
/**
 * Created by PhpStorm.
 * User: imkrimerman
 * Date: 10/27/14
 * Time: 15:08
 */

/**
 * @param $array
 * @return Container
 */
function a( $array = array() )
{
    return new Container( $array );
}

// ------------------------------------------------------------------------------

/**
 * @param $string
 * @return String
 */
function s( $string = '' )
{
    return new String($string);
}
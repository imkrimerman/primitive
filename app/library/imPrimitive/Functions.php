<?php

/**
 * @param array $array
 * @return \im\Primitive\Container
 */
function a( $array = array() )
{
    return new im\Primitive\Container( $array );
}

// ------------------------------------------------------------------------------


/**
 * @param string $string
 * @return \im\Primitive\String
 */
function s( $string = '' )
{
    return new im\Primitive\String($string);
}

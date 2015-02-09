<?php namespace im\Primitive\Support\Traits;

use JWT;
use Exception;
use im\Primitive\Support\Str;

/**
 * Class StringCheckerTrait
 *
 * @package im\Primitive\Support\Traits
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
trait StringCheckerTrait {

    /**
     * Check if string is encrypted Container
     *
     * @param string $encrypted
     * @param string $key
     * @return bool
     */
    public function isEncryptedContainer($encrypted, $key)
    {
        try
        {
            $data = JWT::decode($encrypted, $key);
        }
        catch(Exception $e)
        {
            return false;
        }

        return $this->isJson($data->container);
    }

    /**
     * Check if string is encrypted String
     *
     * @param string $encrypted
     * @param string $key
     * @return bool
     */
    public function isEncryptedString($encrypted, $key)
    {
        try
        {
            $data = JWT::decode($encrypted, $key);
        }
        catch(Exception $e)
        {
            return false;
        }

        return is_string($data->string);
    }

    /**
     * Check if string is readable file
     *
     * @param string $string
     * @return bool
     */
    public function isFile($string)
    {
        return Str::isFile($string);
    }


    /**
     * Checks if given string is Json
     *
     * @param string $string
     * @return bool
     */
    public function isJson($string)
    {
        return Str::isJson($string);
    }


    /**
     * Checks if given string is serialized
     *
     * @param string $string
     * @return bool
     */
    public function isSerialized($string)
    {
        return Str::isSerialized($string);
    }
}

<?php namespace im\Primitive\Support\Traits;

use im\Primitive\Support\Str;
use JWT;
use UnexpectedValueException;


trait StringCheckerTrait {

    /**
     * Check if string is encrypted Container
     *
     * @param $encrypted
     *
     * @param $key
     *
     * @return bool
     */
    public function isEncryptedContainer($encrypted, $key)
    {
        try
        {
            $data = JWT::decode($encrypted, $key);
        }
        catch(UnexpectedValueException $e)
        {
            return false;
        }

        return $this->isJson($data->container);
    }

    /**
     * Check if string is readable file
     *
     * @param string $string
     *
     * @return bool
     */
    public function isFile($string)
    {
        return Str::isFile($string);
    }


    /**
     * Checks if given string is Json
     *
     * @param $string
     *
     * @return bool
     */
    public function isJson($string)
    {
        return Str::isJson($string);
    }


    /**
     * Checks if given string is serialized
     *
     * @param $string
     *
     * @return bool
     */
    public function isSerialized($string)
    {
        return Str::isSerialized($string);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 26.10.14
 * Time: 14:25
 */
namespace im\Primitive\Interfaces {

    interface JsonableInterface
    {
        public function toJson();

        public function fromJson( $json );
    }

}


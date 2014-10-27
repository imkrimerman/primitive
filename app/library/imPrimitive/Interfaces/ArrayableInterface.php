<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 26.10.14
 * Time: 14:23
 */
namespace im\Primitive\Interfaces {

    interface ArrayableInterface
    {
        public function toArray();

        public function fromArray( array $array = array() );
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 26.10.14
 * Time: 19:04
 */

namespace im\Primitive\Interfaces {

    interface FileableInterface
    {
        public function toFile( $file );

        public function fromFile( $file );
    }

}
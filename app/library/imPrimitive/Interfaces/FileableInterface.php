<?php
/**
 * Created by Igor Krimerman.
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
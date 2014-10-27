<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 27.10.14
 * Time: 20:27
 */

namespace im\Primitive\Interfaces {

    interface RevertableInterface
    {
        public function revert();

        public function save();
    }

}
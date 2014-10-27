<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 26.10.14
 * Time: 14:23
 */
interface ArrayInterface {
    public function toArray();
    public function fromArray( array $array = array() );
}
<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 26.10.14
 * Time: 19:04
 */

interface FileInterface {
    public function toFile();
    public function fromFile( $file );
} 
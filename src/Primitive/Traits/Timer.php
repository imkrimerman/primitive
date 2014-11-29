<?php
/**
 * Created by PhpStorm.
 * User: Nastya
 * Date: 30.10.14
 * Time: 20:26
 */

namespace im\Primitive\Traits;


trait Timer
{
    public $start = 0;
    public $point = 0;
    public $end   = 0;

    // --------------------------------------------------------------------------

    public function start()
    {
        $this->start = time();

        return $this;
    }

    // --------------------------------------------------------------------------

    public function stop()
    {
        $this->end = time();

        return $this;
    }

    // --------------------------------------------------------------------------

    public function result()
    {
        return $this->end - $this->start;
    }

    // --------------------------------------------------------------------------

    public function point()
    {
        $this->point = time();

        return $this;
    }
} 
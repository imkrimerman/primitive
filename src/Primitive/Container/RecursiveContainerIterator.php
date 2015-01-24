<?php namespace im\Primitive\Container;

use RecursiveArrayIterator;
use RecursiveIterator;
use im\Primitive\Support\Contracts\ArrayableInterface;


class RecursiveContainerIterator extends RecursiveArrayIterator/* implements RecursiveIterator*/ {

//    /**
//     * @var array
//     */
//    protected $items;
//    /**
//     * @var int
//     */
//    protected $position;
//
//
//    /**
//     * @param $items
//     */
//    public function __construct($items)
//    {
//        $this->items = $items;
//
//        $this->position = 0;
//    }
//
//    /**
//     * (PHP 5 &gt;= 5.0.0)<br/>
//     * Return the current element
//     * @link http://php.net/manual/en/iterator.current.php
//     * @return mixed Can return any type.
//     */
//    public function current()
//    {
//        return $this->items[$this->position];
//    }
//
//    /**
//     * (PHP 5 &gt;= 5.0.0)<br/>
//     * Move forward to next element
//     * @link http://php.net/manual/en/iterator.next.php
//     * @return void Any returned value is ignored.
//     */
//    public function next()
//    {
//        ++$this->position;
//    }
//
//    /**
//     * (PHP 5 &gt;= 5.0.0)<br/>
//     * Return the key of the current element
//     * @link http://php.net/manual/en/iterator.key.php
//     * @return mixed scalar on success, or null on failure.
//     */
//    public function key()
//    {
//        return $this->position;
//    }
//
//    /**
//     * (PHP 5 &gt;= 5.0.0)<br/>
//     * Checks if current position is valid
//     * @link http://php.net/manual/en/iterator.valid.php
//     * @return boolean The return value will be casted to boolean and then evaluated.
//     * Returns true on success or false on failure.
//     */
//    public function valid()
//    {
//        return array_key_exists($this->position, $this->items);
//    }
//
//    /**
//     * (PHP 5 &gt;= 5.0.0)<br/>
//     * Rewind the Iterator to the first element
//     * @link http://php.net/manual/en/iterator.rewind.php
//     * @return void Any returned value is ignored.
//     */
//    public function rewind()
//    {
//        $this->position = 0;
//    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Returns if an iterator can be created for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     */
    public function hasChildren()
    {
        $current = $this->current();

        return $this->isArrayable($current) && count($current);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Returns an iterator for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return RecursiveIterator An iterator for the current entry.
     */
    public function getChildren()
    {
        return new static($this->getArrayable($this->current()));
    }

    /**
     * @param $items
     *
     * @return bool
     */
    private function isArrayable($items)
    {
        return is_array($items) || $items instanceof Container || $items instanceof ArrayableInterface;
    }

    /**
     * @param $items
     *
     * @return array
     */
    private function getArrayable($items)
    {
        if ($items instanceof Container)
        {
            $items = $items->all();
        }
        elseif ($items instanceof ArrayableInterface)
        {
            $items = $items->toArray();
        }

        return $items;
    }
}

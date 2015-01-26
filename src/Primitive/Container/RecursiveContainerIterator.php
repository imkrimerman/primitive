<?php namespace im\Primitive\Container;

use RecursiveArrayIterator;
use im\Primitive\Support\Contracts\ArrayableInterface;


class RecursiveContainerIterator extends RecursiveArrayIterator {

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

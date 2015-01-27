<?php namespace im\Primitive\Container;

use im\Primitive\Support\Contracts\RevertableInterface;


class RevertableContainer extends Container implements RevertableInterface {

    /*
    |--------------------------------------------------------------------------
    | Storing clone of main items, used for reverting
    |--------------------------------------------------------------------------
    */
    protected $clone;

    /**
     * Initialize items from array
     *
     * @param $array
     *
     * @return $this
     */
    protected function initialize($array)
    {
        parent::initialize($array);

        return $this->assignClone();
    }


    /**
     * Saves Container state to clone, to revert in future
     *
     * @return $this
     */
    public function save()
    {
        return $this->assignClone();
    }

    /**
     * Reverts Container state from clone
     *
     * @return $this
     */
    public function revert()
    {
        $this->items = $this->clone;

        return $this;
    }


    /**
     * @return array
     */
    public function getClone()
    {
        return $this->clone;
    }

    /**
     * @return $this
     */
    protected function assignClone()
    {
        $this->clone = $this->items;

        return $this;
    }
}

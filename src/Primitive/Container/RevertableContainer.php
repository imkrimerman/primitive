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
     * @param array $array
     *
     * @return $this
     */
    protected function initialize(array $array)
    {
        parent::initialize($array);

        $this->clone = $this->items;

        return $this;
    }

    /**
     * Saves Container state to clone, to revert in future
     *
     * @return $this
     */
    public function save()
    {
        $this->clone = $this->items;

        return $this;
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
}

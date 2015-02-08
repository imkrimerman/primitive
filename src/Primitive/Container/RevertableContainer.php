<?php namespace im\Primitive\Container;

use im\Primitive\Support\Contracts\RevertableContract;

/**
 * Class RevertableContainer
 *
 * @package im\Primitive\Container
 * @author Igor Krimerman <i.m.krimerman@gmail.com>
 */
class RevertableContainer extends Container implements RevertableContract {

    /**
     * Storing clone of main items, used for reverting
     * @var array
     */
    protected $clone;

    /**
     * {@inheritdoc}
     */
    protected function initialize($value)
    {
        parent::initialize($value);

        return $this->assignClone();
    }


    /**
     * Save Container state to clone, to revert in future
     *
     * @return $this
     */
    public function save()
    {
        return $this->assignClone();
    }

    /**
     * Revert Container state from clone
     *
     * @return $this
     */
    public function revert()
    {
        $this->items = $this->clone;

        return $this;
    }


    /**
     * Getter for clone
     *
     * @return array
     */
    public function getClone()
    {
        return $this->clone;
    }

    /**
     * Assign inner items to clone
     *
     * @return $this
     */
    protected function assignClone()
    {
        $this->clone = $this->items;

        return $this;
    }
}

<?php

namespace Pitpit\Component\Diff;

/**
 * An object to compare two variable values.
 *
 * @author    Damien Pitard <damien.pitard@gmail.com>
 */
class Diff implements \Iterator, \ArrayAccess, \Countable
{
    const STATUS_SAME = 'same';
    const STATUS_CREATED = 'created';
    const STATUS_MODIFIED = 'modified';
    const STATUS_DELETED = 'deleted';
    const STATUS_TYPE_CHANGED = 'type_changed';

    protected $identifier;
    protected $status;
    protected $old;
    protected $new;
    protected $array = array();
    protected $diffs = array();

    /**
     * Constructor
     *
     * @param mixed $old The old value
     * @param mixed $new The new value
     */
    public function __construct($old, $new, $identifier = null)
    {
        $this->status = self::STATUS_SAME;
        $this->identifier = $identifier;
        $this->old = $old;
        $this->new = $new;
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set the status of this Diff
     *
     * @param integer $status The status this Diff.
     *
     * @throws InvalidArgumentException If given status is not equals to one of the Diff::STATUS_ constant
     */
    public function setStatus($status)
    {
        if ($status !== self::STATUS_SAME
            && $status !== self::STATUS_CREATED
            && $status !== self::STATUS_MODIFIED
            && $status !== self::STATUS_DELETED
            && $status !== self::STATUS_TYPE_CHANGED) {

            throw new \InvalidArgumentException(sprintf('Invalid status value "%s"', $status));
        }

        $this->status = $status;
    }

    /**
     * Return the nummber of children
     *
     * @see Countable
     *
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function valid()
    {
        return (false !== current($this->diffs));
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Diff) {
            throw new \InvalidArgumentException("Not a Diff instance");
        }

        if (null === $offset) {
            $this->array[] = $value;
        } else {
            $this->array[$offset] = $value;
        }

        $this->addDiff($this->array[$offset]);

        reset($this->array);
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('not implemented');
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->array[$offset])?$this->array[$offset]:null;
    }

    /**
     *
     * @param Diff
     */
    protected function addDiff(Diff $diff)
    {
        $this->diffs[spl_object_hash($diff)] = $diff;
    }

    /**
     * Dynamically add an object property to compare values of a property in the origin object.
     *
     * @param string $name The name of the compared property
     * @param Diff   $diff The Diff comparing the values of a property in the origin object
     */
    public function addProperty($name, Diff $diff)
    {
        //declare a new public property
        $this->{$name} = $diff;

        $this->addDiff($this->{$name});
    }

    /**
     * Get the old value of the variable
     *
     * @return mixed|null Null if the variable has been created
     */
    public function getOld()
    {
        return $this->old;
    }

    /**
     * Get the new value of the variable
     *
     * @return mixed|null Null if the variable has been deleted
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Does the variable has been created
     *
     * @return boolean
     */
    public function isCreated()
    {
        return (self::STATUS_CREATED === $this->status);
    }

    /**
     * Does the variable has been deleted
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return (self::STATUS_DELETED === $this->status);
    }

    /**
     * Is the variable modified
     *
     * @return boolean
     */
    public function isModified()
    {
        return (self::STATUS_MODIFIED === $this->status || self::STATUS_TYPE_CHANGED === $this->status);
    }

    /**
     * If the variable is modified and is uncomparable deeply (not the same type)
     */
    public function isTypeChanged()
    {
        return (self::STATUS_TYPE_CHANGED === $this->status);
    }

    /**
     * Is the variable is the same
     *
     * @return boolean
     */
    public function isSame()
    {
        return (self::STATUS_SAME === $this->status);
    }

    /**
     * Gets the value of status.
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
}
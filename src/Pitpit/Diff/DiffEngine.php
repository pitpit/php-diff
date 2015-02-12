<?php

namespace Pitpit\Diff;

/**
 * DiffEngine.
 *
 * @author    Damien Pitard <damien.pitard@gmail.com>
 */
class DiffEngine
{
    protected $map;

    /**
     * Set methods or properties you want to compare in a classe
     *
     * @param array $map The properties and methods tou want to compare (where keys are  full classname and values the properties or method)
     */
    public function __construct(array $map = array())
    {
        $this->map = $map;
    }

    /**
     * Generate a diff beetween to variable
     *
     * @param mixed  $old    An object
     * @param mixed  $new    An object
     * @param string $status Force a status
     *
     * @return Diff
     */
    public function compare($old, $new, $status = null)
    {
        $diff = new Diff($old, $new);

        if (is_object($old) || is_object($new)) {

            //$old or $new could be null

            if ((!is_null($old) && !is_object($old)) || (!is_null($new) && !is_object($new))) {

                throw new \InvalidArgumentException(sprintf('Unable to compare an object to a non object (%s -> %s)', print_r($old, true), print_r($new, true)));
            }

            if (!is_null($old) && !is_null($new) && get_class($old) !== get_class($new)) {
                throw new \InvalidArgumentException('Unable to compare objects of different classes');
            }

            $reflectionOld = !is_null($old)?new \ReflectionClass($old):null;
            $reflectionNew = !is_null($new)?new \ReflectionClass($new):null;

            $done = array();
            if (!is_null($reflectionNew) && isset($this->map[get_class($new)])) {
                foreach ($this->map[get_class($new)] as $name) {
                    $property = $reflectionNew->getProperty($name);
                    $property->setAccessible(true);

                    if (!is_null($reflectionOld) && $reflectionOld->hasProperty($name)) {
                        $oldProperty = $reflectionOld->getProperty($name);
                        $oldProperty->setAccessible(true);
                        $subdiff = $this->compare($oldProperty->getValue($old), $property->getValue($new));
                        if (is_null($status) && $subdiff->getModified()) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                    } else {
                        $subdiff = $this->compare(null, $property->getValue($new), Diff::STATUS_CREATED);
                        if (is_null($status)) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                    }

                    $diff->addProperty($name, $subdiff);


                    $done[$name] = true;
                }
            }

            if (!is_null($reflectionOld) && isset($this->map[get_class($old)])) {
                foreach ($this->map[get_class($old)] as $name) {
                    $property = $reflectionOld->getProperty($name);
                    if (!isset($done[$name])) {
                        $property->setAccessible(true);
                        $subdiff = $this->compare($property->getValue($old), null, Diff::STATUS_DELETED);
                        if (is_null($status)) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                        $diff->addProperty($name, $subdiff);
                    }
                }
            }
        } else if (is_array($old) || is_array($new)) {

            $done = array();
            if (is_array($new)) {
                foreach ($new as $key => $value) {
                    if (is_array($old) && isset($old[$key])) {
                        $subdiff = $this->compare($old[$key], $value);
                        if (is_null($status) && $subdiff->getModified()) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                        $diff[$key] = $subdiff;
                    } else {
                        $diff[$key] = $this->compare(null, $value, Diff::STATUS_CREATED);
                        if (is_null($status)) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                    }

                    $done[$key] = true;
                }
            }

            if (is_array($old)) {
                foreach ($old as $key => $value) {
                    if (!isset($done[$key])) {
                        $diff[$key] = $this->compare($value, null, Diff::STATUS_DELETED);
                        if (is_null($status)) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                    }
                }
            }
        } else {
            //scalar objects
            if (is_null($status) && $old !== $new) {
                $status = Diff::STATUS_MODIFIED;
            }
        }

        if (is_null($status)) {
            $status = Diff::STATUS_UNCHANGED;
        }

        $diff->setStatus($status);

        return $diff;
    }
}
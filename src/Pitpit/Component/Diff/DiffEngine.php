<?php

namespace Pitpit\Component\Diff;

/**
 * DiffEngine.
 *
 * @author    Damien Pitard <damien.pitard@gmail.com>
 */
class DiffEngine
{
    protected $filter;
    protected $compares;

    /**
     * Set methods or properties you want to compare in a classe
     *
     * @param ReflectionProperty constants (see http://php.net/manual/en/class.reflectionproperty.php)
     *
     */
    public function __construct($filter = null, $compares = array())
    {
        $this->filter = $filter;
        $this->compares = $compares;
    }

    /**
     * Generate a diff beetween to variable
     *
     * @param mixed  $old    An object
     * @param mixed  $new    An object
     * @param string $identifier   A name for the variable
     * @param string $status Force a status
     *
     * @return Diff
     */
    public function compare($old, $new, $identifier = null)
    {
        $diff = new Diff($old, $new, $identifier);

        if (null === $new) {
            if (null === $old) {
                //same type, same value
                //nothing to do
            } else {
                //new variable is null but not the old
                $diff->setStatus(Diff::STATUS_DELETED);
            }
        } else if (is_object($new)) {
            if (null === $old) {
                //the new variable is an object but the old is null
                $diff->setStatus(Diff::STATUS_CREATED);
            } else if (is_object($old)) {
                //the both variables are objects

                $reflectionOld = new \ReflectionClass($old);
                $reflectionNew = new \ReflectionClass($new);

                if ($reflectionNew->getName() !== $reflectionOld->getName()) {
                    //not the same class
                    $diff->setStatus(Diff::STATUS_TYPE_CHANGED);
                } else {
                    //same class


                    //check if a compare closure exists
                    if (isset($this->compares[$reflectionNew->getName()])) {

                        $this->compares[$reflectionNew->getName()]($diff);
                    } else {

                        //this is a map of properties to check when comparing
                        $map = array_merge($this->buildMap($reflectionOld), $this->buildMap($reflectionNew));

                        $done = array();

                        //parse new object
                        if (isset($map[$reflectionNew->getName()])) {
                            foreach ($map[$reflectionNew->getName()] as $propertyName) {
                                $property = $reflectionNew->getProperty($propertyName);
                                $property->setAccessible(true);

                                if ($reflectionOld->hasProperty($propertyName)) {
                                    $oldProperty = $reflectionOld->getProperty($propertyName);
                                    $oldProperty->setAccessible(true);
                                    $subdiff = $this->compare($oldProperty->getValue($old), $property->getValue($new), $propertyName);
                                    if ($subdiff->isModified()) {
                                        $diff->setStatus(Diff::STATUS_MODIFIED);
                                    }
                                } else {
                                    $subdiff = $this->compare(null, $property->getValue($new), $propertyName);
                                    $subdiff->setStatus(Diff::STATUS_CREATED);
                                    $diff->setStatus(Diff::STATUS_MODIFIED);
                                }

                                $diff->addProperty($propertyName, $subdiff);

                                $done[$propertyName] = true;
                            }
                        }
                    }
                }
            } else {
                //array or scalar variable (integer, boolean, string)
                $diff->setStatus(Diff::STATUS_TYPE_CHANGED);
            }
        } else if (is_array($new)) {

            if (null === $old) {
                //the new variable is an array but the old is null
                $diff->setStatus(Diff::STATUS_CREATED);
            } else if (is_object($old)) {
                //the new variable is an array but the old is an object
                $diff->setStatus(Diff::STATUS_TYPE_CHANGED);
            } else if (is_array($old)) {
                //the both variables are arrays

                $done = array();

                //parse new array
                foreach ($new as $key => $value) {
                    if (isset($old[$key])) {
                        //an old value exists
                        $subdiff = $this->compare($old[$key], $value, $key);
                        if ($subdiff->isModified() || $subdiff->isCreated() || $subdiff->isDeleted()) {
                            $diff->setStatus(Diff::STATUS_MODIFIED);
                        }
                    } else {
                        $subdiff = new Diff(null, $value, $key);
                        $subdiff->setStatus(Diff::STATUS_CREATED);
                        $diff->setStatus(Diff::STATUS_MODIFIED);
                    }

                    $diff[$key] = $subdiff;
                    $done[$key] = true;
                }

                //parse old array
                foreach ($old as $key => $value) {
                    if (!isset($done[$key])) {
                        //no new value exists
                        $subdiff = new Diff($value, null, $key);
                        $subdiff->setStatus(Diff::STATUS_DELETED);
                        $diff->setStatus(Diff::STATUS_MODIFIED);
                        $diff[$key] = $subdiff;
                    }
                }

            } else {
                //scalar variable (integer, boolean, string)
                $diff->setStatus(Diff::STATUS_TYPE_CHANGED);
            }
        } else {

            //scalar variable (integer, boolean, string)

            if (null === $old) {
                //the new variable is a scalar but the old is null

                // $diff->setStatus(Diff::STATUS_CREATED);

                $diff->setStatus(Diff::STATUS_MODIFIED);
            } else if (is_object($old)) {
                //the new variable is a scalar but the old is an object
                $diff->setStatus(Diff::STATUS_TYPE_CHANGED);
            } else if (is_array($old)) {
                $diff->setStatus(Diff::STATUS_TYPE_CHANGED);
            } else if ($old !== $new) {
                $diff->setStatus(Diff::STATUS_MODIFIED);
            }
        }

        return $diff;
    }


    /**
     * Build a map of properties to scan for diffs by class
     *
     * @return array An array where keys are fully classified class names and value arrays of properties
     */
    protected function buildMap(\ReflectionClass $reflection = null)
    {
        $map = array();
        if (!is_null($reflection)) {
            $class = $reflection->getName();

            $map[$class] = array();
            if ($this->filter) {
                $properties = $reflection->getProperties($this->filter);
            } else {
                $properties = $reflection->getProperties();
            }

            foreach ($properties as $property) {
                $map[$class][] = $property->getName();
            }
        }

        return $map;
    }
}

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
    protected $excludes;

    /**
     * Set methods or properties you want to compare in a classe
     *
     * @param ReflectionProperty constants (see http://php.net/manual/en/class.reflectionproperty.php)
     * @param array Mapping of class and properties to exclude
     *
     */
    public function __construct($filter = null, $excludes = array())
    {
        $this->filter = $filter;
        $this->excludes = $excludes;
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
    public function compare($old, $new, $identifier = null, $status = null)
    {
        $diff = new Diff($identifier, $old, $new);

        //compare an objet to something else
        if (is_object($old) || is_object($new)) {

            //$old or $new could be null
            if ((!is_null($old) && !is_object($old)) || (!is_null($new) && !is_object($new))) {

                $diff->setStatus(Diff::STATUS_TYPE_CHANGE);

                return $diff;
                //throw new \InvalidArgumentException(sprintf('Unable to compare type "%s" (old) to type "%s" (new), variable: %s', gettype($old), gettype($new), $identifier?$identifier:'null'));
            }

            if (!is_null($old) && !is_null($new) && get_class($old) !== get_class($new)) {

                $diff->setStatus(Diff::STATUS_TYPE_CHANGE);

                return $diff;
                //throw new \InvalidArgumentException(sprintf('Unable to compare objects of different classes, identifier: %s', $identifier?$identifier:'null'));
            }



            $reflectionOld = !is_null($old)?new \ReflectionClass($old):null;
            $reflectionNew = !is_null($new)?new \ReflectionClass($new):null;

            $map = array_merge($this->buildMap($reflectionOld), $this->buildMap($reflectionNew));
            // $map = array();

            // if (!is_null($reflectionOld)) {
            //     $map[get_class($old)] = array();
            //     if ($this->filter) {
            //         $properties = $reflectionOld->getProperties($this->filter);
            //     } else {
            //         $properties = $reflectionOld->getProperties();
            //     }
            //     foreach ($properties as $property) {
            //         if (!isset($this->exclude[get_class($old)]) || !in_array($property->getName(), $this->exclude[get_class($old)])) {
            //             $map[get_class($old)][] = $property->getName();
            //         }
            //     }
            // }

            // if (!is_null($reflectionNew)) {
            //     $map[get_class($new)] = array();
            //     if ($this->filter) {
            //         $properties = $reflectionNew->getProperties($this->filter);
            //     } else {
            //         $properties = $reflectionNew->getProperties();
            //     }
            //     foreach ($properties as $property) {
            //         $map[get_class($new)][] = $property->getName();
            //     }
            // }

            $done = array();
            if (!is_null($reflectionNew) && isset($map[get_class($new)])) {
                foreach ($map[get_class($new)] as $name) {
                    $property = $reflectionNew->getProperty($name);
                    $property->setAccessible(true);

                    if (!is_null($reflectionOld) && $reflectionOld->hasProperty($name)) {
                        $oldProperty = $reflectionOld->getProperty($name);
                        $oldProperty->setAccessible(true);
                        $subdiff = $this->compare($oldProperty->getValue($old), $property->getValue($new), $name);
                        if (is_null($status) && $subdiff->isModified()) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                    } else {
                        $subdiff = $this->compare(null, $property->getValue($new), $name, Diff::STATUS_CREATED);
                        if (is_null($status)) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                    }

                    $diff->addProperty($name, $subdiff);


                    $done[$name] = true;
                }
            }

            if (!is_null($reflectionOld) && isset($map[get_class($old)])) {
                foreach ($map[get_class($old)] as $name) {
                    $property = $reflectionOld->getProperty($name);
                    if (!isset($done[$name])) {
                        $property->setAccessible(true);
                        $subdiff = $this->compare($property->getValue($old), null, $name, Diff::STATUS_DELETED);
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
                        $subdiff = $this->compare($old[$key], $value, $key);
                        if (is_null($status) && $subdiff->isModified()) {
                            $status = Diff::STATUS_MODIFIED;
                        }
                        $diff[$key] = $subdiff;
                    } else {
                        $diff[$key] = $this->compare(null, $value, $key, Diff::STATUS_CREATED);
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
                        $diff[$key] = $this->compare($value, null, $key, Diff::STATUS_DELETED);
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
            $status = Diff::STATUS_SAME;
        }

        $diff->setStatus($status);

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
                if (!isset($this->excludes[$class]) || !in_array($property->getName(), $this->excludes[$class])) {
                    $map[$class][] = $property->getName();
                }
            }
        }

        return $map;
    }
}

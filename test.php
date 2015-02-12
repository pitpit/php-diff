<?php

include __DIR__ . "/vendor/autoload.php";

class MyClassToCompare
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
   {
    return $this->value;
   }
}

$toCompare1 = new MyClassToCompare(4);
$toCompare2 = new MyClassToCompare(9);

$engine = new \Pitpit\Diff\DiffEngine(array(
    'MyClassToCompare' => array(
        'value'
    )
));
$diffs = $engine->compare($toCompare1, $toCompare2);

var_dump($diffs->getNew());

    // /**
    //  * Does the variable has been created
    //  *
    //  * @return boolean
    //  */
    // public function getCreated()
    // {
    //     return (self::STATUS_CREATED === $this->status);
    // }

    // /**
    //  * Does the variable has been deleted
    //  *
    //  * @return boolean
    //  */
    // public function getDeleted()
    // {
    //     return (self::STATUS_DELETED === $this->status);
    // }

    // /**
    //  * Is the variable modified
    //  *
    //  * @return boolean
    //  */
    // public function getModified()
    // {
    //     return (self::STATUS_MODIFIED === $this->status);
    // }

    // /**
    //  * Is the variable is changed
    //  *
    //  * @return boolean
    //  */
    // public function getUnchanged()
    // {
    //     return (self::STATUS_UNCHANGED === $this->status);
    // }



PHP Diff
========

A comprehensive library for get differences between two variables (array, object, string...)

Installation
------------

```
php composer.phar require "pitpit/diff":"@dev"
```

Usage
-----

## Comparing string

```php
$engine = new \Pitpit\Component\Diff\DiffEngine();
$diff = $engine->compare('test1', 'test2');

echo $diff->isModified();
```

## Comparing objects

```php

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

$engine = new \Pitpit\Component\Diff\DiffEngine();

$diff = $engine->compare($toCompare1, $toCompare2);

//this closure iterate on each child properties and display where differences are
$trace = function($diff, $tab = '') use (&$trace) {

    foreach ($diff as $element) {
        $c = $element->isTypeChanged()?'T':($element->isModified()?'M':($element->isCreated()?'+':($element->isDeleted()?'-':'=')));

        // print_r(sprintf("%s* %s [%s -> %s] (%s)\n", $tab, $element->getIdentifier(), is_object($element->getOld())?get_class($element->getOld()):gettype($element->getOld()), is_object($element->getNew())?get_class($element->getNew()):gettype($element->getNew()), $c));
        print_r(sprintf("%s* %s [%s -> %s] (%s)\n", $tab, $element->getIdentifier(), gettype($element->getOld()), gettype($element->getNew()), $c));


        if ($diff->isModified()) {
            $trace($element, $tab . '  ');
        }
    }
};
```

Run the tests
-------------

Get and install composer: https://getcomposer.org/doc/00-intro.md#installation-nix

The resolve and download dependencies:

    php composer.phar install

Run the tests:

    phpunit


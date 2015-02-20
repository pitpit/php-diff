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

if ($diff->isModified()) {

    //iterate on each child item to compare (properties, methods, constants)
    foreach ($diff as $element) {
        print_r("----\n");
        print_r('id: ' . $element->getIdentifier()."\n");
        print_r('modified: ' . $element->isModified()."\n");
        print_r('created: ' . $element->isCreated()."\n");
        print_r('deleted: ' . $element->isDeleted()."\n");
        print_r('old: ' . print_r($element->getOld(), true)."\n");
        print_r('new: ' . print_r($element->getNew(), true)."\n");
    }
}
```

Run the tests
-------------

Get and install composer: https://getcomposer.org/doc/00-intro.md#installation-nix

The resolve and download dependencies:

    php composer.phar install

Run the tests:

    phpunit


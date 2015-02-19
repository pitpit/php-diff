PHP Diff
========

A comprehensive library for get differences between two variables (array, object, string...)

Installation
------------

```
php composer.phar require pitpit/diff:@dev
```

Usage
-----

```php
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

$engine = new \Pitpit\Component\Diff\DiffEngine(array(
    'MyClassToCompare' => array(
        'value'
    )
));

$diffs = $engine->compare($toCompare1, $toCompare2);
```

Run the tests
-------------

Get and install composer: https://getcomposer.org/doc/00-intro.md#installation-nix

The resolve and download dependencies:

    php composer.phar install

Run the tests:

    phpunit


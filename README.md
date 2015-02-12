PHP Diff
========

PHP Component to compare variables easily (array, object, string).

Usage
-----

### Compring objects

class MyClassToCompare {

    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

$toCompare1 = new MyClassToCompare(4);
$toCompare2 = new MyClassToCompare(9);

$engine = new Pitpit\Diff\DiffEngine();
$diff = $engine->compare($toCompare1, $toCompare2);

Run the tests
-------------

Get and install composer: https://getcomposer.org/doc/00-intro.md#installation-nix

The resolve and download dependencies:

    php composer.phar install

Run the tests:

    phpunit


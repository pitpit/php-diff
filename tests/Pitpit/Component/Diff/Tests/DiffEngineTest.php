<?php

namespace Pitpit\Component\Diff\Tests;

use Pitpit\Component\Diff\DiffEngine;
use Pitpit\Component\Diff\Diff;

class DiffEngineTest extends \PHPUnit_Framework_TestCase
{

    function testDummyCompare()
    {
        $engine = new DiffEngine();
        //created
        $diff = $engine->compare(null, null);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(null, $diff->getOld());
        $this->assertEquals(null, $diff->getNew());
    }

    function testCompareStrings()
    {
        $engine = new DiffEngine();
        $var1 = 'foo1';
        $var2 = 'foo2';
        $var3 = 'foo2';

        //modified
        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals('foo1', $diff->getOld());
        $this->assertEquals('foo2', $diff->getNew());

        //same
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertTrue($diff->isSame());
        $this->assertEquals('foo2', $diff->getOld());
        $this->assertEquals('foo2', $diff->getNew());
    }

    function testCompareNullToStrings()
    {
        $engine = new DiffEngine();
        $var1 = 'foo1';

        $diff = $engine->compare(null, $var1);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(null, $diff->getOld());
        $this->assertEquals('foo1', $diff->getNew());
    }

    function testCompareInteger()
    {
        $engine = new DiffEngine();
        $var1 = 1;
        $var2 = 2;
        $var3 = 2;

        //modified
        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(1, $diff->getOld());
        $this->assertEquals(2, $diff->getNew());

        //unchanged
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(2, $diff->getOld());
        $this->assertEquals(2, $diff->getNew());
    }

    function compareNullToInteger()
    {
        $engine = new DiffEngine();
        $var1 = 1;

        //created
        $diff = $engine->compare(null, $var1);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(null, $diff->getOld());
        $this->assertEquals(1, $diff->getNew());

    }

    function testCompareBoolean()
    {
        $engine = new DiffEngine();
        $var1 = false;
        $var2 = true;
        $var3 = true;

        //modified
        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(false, $diff->getOld());
        $this->assertEquals(true, $diff->getNew());

        //uchanged
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(true, $diff->getOld());
        $this->assertEquals(true, $diff->getNew());
    }

    function testCompareNullToBoolean()
    {
        $engine = new DiffEngine();
        $var1 = false;
        $var2 = true;
        $var3 = true;

        //created
        $diff = $engine->compare(null, $var1);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(null, $diff->getOld());
        $this->assertEquals(false, $diff->getNew());
    }

    function testCompareArray()
    {
        $engine = new DiffEngine();
        $var1 = array('foo1');
        $var2 = array('foo2', 'foo3');
        $var3 = array('foo2', 'foo3');

        //modified
        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals(array('foo2', 'foo3'), $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo1', $subdiff->getOld());
        $this->assertEquals('foo2', $subdiff->getNew());

        $subdiff = $diff[1];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);

        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('foo3', $subdiff->getNew());

        //unchanged
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo2', 'foo3'), $diff->getOld());
        $this->assertEquals(array('foo2', 'foo3'), $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo2', $subdiff->getOld());
        $this->assertEquals('foo2', $subdiff->getNew());

        $subdiff = $diff[1];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo3', $subdiff->getOld());
        $this->assertEquals('foo3', $subdiff->getNew());

    }
    function testCompareArrayCountable()
    {
        $engine = new DiffEngine();
        $var1 = array('foo1', 'foo3');
        $var2 = array('foo2', 'foo3');

        //created
        $diff = $engine->compare($var1, $var2);

        //test iterator
        $this->assertEquals(2, count($diff));
    }


    function testCompareNullToArray()
    {
        $engine = new DiffEngine();
        $var1 = array('foo1');

        //created
        $diff = $engine->compare(null, $var1);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertTrue($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(null, $diff->getOld());
        $this->assertEquals(array('foo1'), $diff->getNew());

        //deleted
        $diff = $engine->compare($var1, null);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertTrue($diff->isDeleted());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals(null, $diff->getNew());
    }

    function testCompareScalarToArray()
    {
        $engine = new DiffEngine();
        $var1 = 'foo1';
        $var2 = array('foo2');

        //created
        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals('foo1', $diff->getOld());
        $this->assertEquals(array('foo2'), $diff->getNew());


        $var1 = array('foo1');
        $var2 = 'foo2';

        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals('foo2', $diff->getNew());

    }


    function testCompareScalarToArrayCountable()
    {
        $engine = new DiffEngine();
        $var1 = 'foo1';
        $var2 = array('foo2');

        //created
        $diff = $engine->compare($var1, $var2);

        //test iterator
        $this->assertEquals(0, count($diff));
    }

    function testCompareNestedArray()
    {
        $engine = new DiffEngine();
        $var1 = array('foo1');
        $var2 = array(array('bar1', 'bar2'));

        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals(array(array('bar1', 'bar2')), $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo1', $subdiff->getOld());
        $this->assertEquals(array('bar1', 'bar2'), $subdiff->getNew());
    }

    function testCompareAssociativeArray()
    {
        $engine = new DiffEngine();
        $var1 = array('foo1' => 'bar1', 'foo3' => 'bar3');
        $var2 = array('foo1' => 'bar2', 'foo2' => 'bar2');

        //created
        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo1' => 'bar1', 'foo3' => 'bar3'), $diff->getOld());
        $this->assertEquals(array('foo1' => 'bar2', 'foo2' => 'bar2'), $diff->getNew());

        $subdiff = $diff['foo1'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('bar1', $subdiff->getOld());
        $this->assertEquals('bar2', $subdiff->getNew());

        $subdiff = $diff['foo3'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals('bar3', $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());

        $subdiff = $diff['foo2'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('bar2', $subdiff->getNew());
    }

    function testCompareNestedAssociativeArray()
    {
        $engine = new DiffEngine();
        $var1 = array('foo1' => 'bar1');
        $var2 = array('foo2' => array('foo2' => 'bar2'), 'foo3' => 'bar3');

        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo1' => 'bar1'), $diff->getOld());
        $this->assertEquals(array('foo2' => array('foo2' => 'bar2'), 'foo3' =>  'bar3'), $diff->getNew());

        $subdiff = $diff['foo1'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals('bar1', $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());


        $subdiff = $diff['foo2'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals(array('foo2' => 'bar2'), $subdiff->getNew());


        $subdiff = $diff['foo3'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('bar3', $subdiff->getNew());
    }

    function testCompareObjects()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = new DiffEngineObject();
        $var2->scalar = 'foo1';
        $var2->scalars[] = 'foo1';

        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals($var1, $diff->getOld());
        $this->assertEquals($var2, $diff->getNew());

        $subdiff = $diff->scalar;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('foo1', $subdiff->getNew());

        $subdiff = $diff->scalars;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array(), $subdiff->getOld());
        $this->assertEquals(array('foo1'), $subdiff->getNew());

    }

    function testCompareNestedObjects()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = new DiffEngineObject();

        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals($var1, $diff->getOld());
        $this->assertEquals($var2, $diff->getNew());

        $object = new DiffEngineObject();
        $object->scalar = 'foo2';
        $var2->objects[] = $object;

        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals($var1, $diff->getOld());
        $this->assertEquals($var2, $diff->getNew());

        $subdiff = $diff->objects;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array(), $subdiff->getOld());
        $this->assertEquals(array($object), $subdiff->getNew());

        $subdiff = $diff->objects[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals($object, $subdiff->getNew());

        $diff = $engine->compare($var2, $var1);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals($var2, $diff->getOld());
        $this->assertEquals($var1, $diff->getNew());

        $subdiff = $diff->objects;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array($object), $subdiff->getOld());
        $this->assertEquals(array(), $subdiff->getNew());

        $subdiff = $diff->objects[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals($object, $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());
    }

    function testCompareObjectsFiltered()
    {
        $engine = new DiffEngine(\ReflectionProperty::IS_PUBLIC);

        $var1 = new DiffEngineObject2();
        $var2 = new DiffEngineObject2('changed', 'changed', 'changed');


        $diff = $engine->compare($var1, $var2);

        $this->assertEquals('changed', $diff->public->getNew());
        $this->assertTrue(isset($diff->public));
        $this->assertFalse(isset($diff->private));
        $this->assertFalse(isset($diff->protected));
        $this->assertEquals(1, count($diff));

        $engine = new DiffEngine(\ReflectionProperty::IS_PRIVATE);
        $var1 = new DiffEngineObject2();
        $var2 = new DiffEngineObject2('changed', 'changed', 'changed');


        $diff = $engine->compare($var1, $var2);

        $this->assertFalse(isset($diff->public));
        $this->assertTrue(isset($diff->private));
        $this->assertFalse(isset($diff->protected));
        $this->assertEquals(1, count($diff));
        $this->assertEquals('changed', $diff->private->getNew());


        $engine = new DiffEngine(\ReflectionProperty::IS_PROTECTED);
        $var1 = new DiffEngineObject2();
        $var2 = new DiffEngineObject2('changed', 'changed', 'changed');


        $diff = $engine->compare($var1, $var2);

        $this->assertFalse(isset($diff->public));
        $this->assertFalse(isset($diff->private));
        $this->assertTrue(isset($diff->protected));
        $this->assertEquals(1, count($diff));
        $this->assertEquals('changed', $diff->protected->getNew());

        $engine = new DiffEngine(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
        $var1 = new DiffEngineObject2();
        $var2 = new DiffEngineObject2('changed', 'changed', 'changed');

        $diff = $engine->compare($var1, $var2);

        $this->assertFalse(isset($diff->public));
        $this->assertTrue(isset($diff->private));
        $this->assertTrue(isset($diff->protected));
        $this->assertEquals(2, count($diff));
        $this->assertEquals('changed', $diff->protected->getNew());
        $this->assertEquals('changed', $diff->private->getNew());
    }

    function testCompareObjectsCompareMethod()
    {
        $engine = new DiffEngine(null, array(
            'Pitpit\Component\Diff\Tests\DiffEngineObject2' => function($diff) {

                //only compare $public properties
                if ($diff->getOld()->public === $diff->getNew()->public) {
                    $diff->setStatus(Diff::STATUS_SAME);
                } else {
                    $diff->setStatus(Diff::STATUS_MODIFIED);
                }
                var_dump($diff);
            }
        ));

        $var1 = new DiffEngineObject2();
        $var2 = new DiffEngineObject2('test', 'changed', 'changed');

        $diff = $engine->compare($var1, $var2);


        $this->assertFalse($diff->isModified());
        $this->assertEquals(0, count($diff));

        $var1 = new DiffEngineObject2();
        $var2 = new DiffEngineObject2('changed', 'changed', 'changed');

        $diff = $engine->compare($var1, $var2);

        $this->assertTrue($diff->isModified());
        $this->assertEquals(0, count($diff));

    }

    function testCompareObjectsCountable()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = new DiffEngineObject();

        $var1->scalar = 'foo1';
        $var2->scalar = 'foo2';

        $diff = $engine->compare($var1, $var2);

        $this->assertEquals(3, count($diff));

    }


    function testCompareObjectsIterator()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = new DiffEngineObject();

        $var1->scalar = 'foo1';
        $var2->scalar = 'foo2';

        $diff = $engine->compare($var1, $var2);

        $i = 0;
        foreach ($diff as $subdiff) {
            $i++;
            $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
            $this->assertFalse($subdiff->isCreated());
            $this->assertFalse($subdiff->isDeleted());

            if ($subdiff->getIdentifier() === 'scalar') {
                $this->assertTrue($subdiff->isModified());
                $this->assertEquals($var1->scalar, $subdiff->getOld());
                $this->assertEquals($var2->scalar, $subdiff->getNew());
            } else if ($subdiff->getIdentifier() === 'scalars') {
                $this->assertFalse($subdiff->isModified());
                $this->assertEquals($var1->scalars, $subdiff->getOld());
                $this->assertEquals($var2->scalars, $subdiff->getNew());
            } else if ($subdiff->getIdentifier() === 'objects') {
                $this->assertFalse($subdiff->isModified());
                $this->assertEquals($var1->objects, $subdiff->getOld());
                $this->assertEquals($var2->objects, $subdiff->getNew());
            }

        }
        $this->assertEquals(3, $i);
    }


    function testCompareObjectToString()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = 'foo';

        $diff = $engine->compare($var1, $var2);

        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());


        $diff = $engine->compare($var2, $var1);

        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());
    }

    function testCompareStringToObject()
    {
        $engine = new DiffEngine();
        $var1 = 'foo';
        $var2 = new DiffEngineObject();

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());

        $diff = $engine->compare($var2, $var1);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());
    }

    function testCompareArrayToObject()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = array('foo');

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());

        $diff = $engine->compare($var2, $var1);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());
    }

    function testCompareObjectToArray()
    {
        $engine = new DiffEngine();
        $var1 = array('foo');
        $var2 = new DiffEngineObject();

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());


        $diff = $engine->compare($var2, $var1);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());
    }


    function testCompareNotSameClass()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = new \StdClass();

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());

        $diff = $engine->compare($var2, $var1);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isTypeChanged());
    }

}

// @codingStandardsIgnoreStart
class DiffEngineObject
{
    public $scalars = array();
    public $scalar;
    public $objects = array();

}

class DiffEngineObject2
{
    public $public = 'test';
    private $private = 'test';
    protected $protected  = 'test';

    public function __construct($public = 'test', $private = 'test', $protected = 'test')
    {
        $this->public = $public;
        $this->private = $private;
        $this->protected  = $protected;
    }
}
// @codingStandardsIgnoreEnd
<?php

namespace Pitpit\Component\Diff\Tests;

use Pitpit\Component\Diff\DiffEngine;

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
        $this->assertEquals(null, $diff->getValue());
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
        $this->assertEquals('foo2', $diff->getValue());
        $this->assertEquals('foo1', $diff->getOld());
        $this->assertEquals('foo2', $diff->getNew());

        //same
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertTrue($diff->isSame());
        $this->assertEquals('foo2', $diff->getValue());
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
        $this->assertEquals('foo1', $diff->getValue());
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
        $this->assertEquals(2, $diff->getValue());
        $this->assertEquals(1, $diff->getOld());
        $this->assertEquals(2, $diff->getNew());

        //unchanged
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(2, $diff->getValue());
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
        $this->assertEquals(1, $diff->getValue());
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
        $this->assertEquals(true, $diff->getValue());
        $this->assertEquals(false, $diff->getOld());
        $this->assertEquals(true, $diff->getNew());

        //uchanged
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(true, $diff->getValue());
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
        $this->assertEquals(false, $diff->getValue());
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
        $this->assertEquals(array('foo2', 'foo3'), $diff->getValue());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals(array('foo2', 'foo3'), $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo2', $subdiff->getValue());
        $this->assertEquals('foo1', $subdiff->getOld());
        $this->assertEquals('foo2', $subdiff->getNew());

        $subdiff = $diff[1];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo3', $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('foo3', $subdiff->getNew());

        //unchanged
        $diff = $engine->compare($var2, $var3);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo2', 'foo3'), $diff->getValue());
        $this->assertEquals(array('foo2', 'foo3'), $diff->getOld());
        $this->assertEquals(array('foo2', 'foo3'), $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo2', $subdiff->getValue());
        $this->assertEquals('foo2', $subdiff->getOld());
        $this->assertEquals('foo2', $subdiff->getNew());

        $subdiff = $diff[1];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo3', $subdiff->getValue());
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
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(array('foo1'), $diff->getValue());
        $this->assertEquals(null, $diff->getOld());
        $this->assertEquals(array('foo1'), $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo1', $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('foo1', $subdiff->getNew());

        //deleted
        $diff = $engine->compare($var1, null);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals(null, $diff->getValue());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals(null, $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals('foo1', $subdiff->getValue());
        $this->assertEquals('foo1', $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());
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
        $this->assertEquals(array('foo2'), $diff->getValue());
        $this->assertEquals('foo1', $diff->getOld());
        $this->assertEquals(array('foo2'), $diff->getNew());


        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo2', $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('foo2', $subdiff->getNew());

        $var1 = array('foo1');
        $var2 = 'foo2';

        $diff = $engine->compare($var1, $var2);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals('foo2', $diff->getValue());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals('foo2', $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals('foo1', $subdiff->getValue());
        $this->assertEquals('foo1', $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());
    }

    function testCompareScalarToArrayIterator()
    {
        $engine = new DiffEngine();
        $var1 = array('foo1');
        $var2 = 'foo2';

        //created
        $diff = $engine->compare($var1, $var2);

        //test iterator
        $i = 0;
        foreach ($diff as $subdiff) {
            $i++;
            $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
            $this->assertFalse($subdiff->isCreated());
            $this->assertFalse($subdiff->isModified());
            $this->assertTrue($subdiff->isDeleted());
            $this->assertEquals('foo1', $subdiff->getValue());
            $this->assertEquals('foo1', $subdiff->getOld());
            $this->assertEquals(null, $subdiff->getNew());

        }
        $this->assertEquals(1, $i);
    }

    function testCompareScalarToArrayCountable()
    {
        $engine = new DiffEngine();
        $var1 = 'foo1';
        $var2 = array('foo2');

        //created
        $diff = $engine->compare($var1, $var2);

        //test iterator
        $this->assertEquals(1, count($diff));
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
        $this->assertEquals(array(array('bar1', 'bar2')), $diff->getValue());
        $this->assertEquals(array('foo1'), $diff->getOld());
        $this->assertEquals(array(array('bar1', 'bar2')), $diff->getNew());

        $subdiff = $diff[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array('bar1', 'bar2'), $subdiff->getValue());
        $this->assertEquals('foo1', $subdiff->getOld());
        $this->assertEquals(array('bar1', 'bar2'), $subdiff->getNew());

        $subdiff2 = $diff[0][0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff2);
        $this->assertTrue($subdiff2->isCreated());
        $this->assertFalse($subdiff2->isModified());
        $this->assertFalse($subdiff2->isDeleted());
        $this->assertEquals('bar1', $subdiff2->getValue());
        $this->assertEquals(null, $subdiff2->getOld());
        $this->assertEquals('bar1', $subdiff2->getNew());

        $subdiff2 = $diff[0][1];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff2);
        $this->assertTrue($subdiff2->isCreated());
        $this->assertFalse($subdiff2->isModified());
        $this->assertFalse($subdiff2->isDeleted());
        $this->assertEquals('bar2', $subdiff2->getValue());
        $this->assertEquals(null, $subdiff2->getOld());
        $this->assertEquals('bar2', $subdiff2->getNew());
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
        $this->assertEquals(array('foo1' => 'bar2', 'foo2' => 'bar2'), $diff->getValue());
        $this->assertEquals(array('foo1' => 'bar1', 'foo3' => 'bar3'), $diff->getOld());
        $this->assertEquals(array('foo1' => 'bar2', 'foo2' => 'bar2'), $diff->getNew());

        $subdiff = $diff['foo1'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('bar2', $subdiff->getValue());
        $this->assertEquals('bar1', $subdiff->getOld());
        $this->assertEquals('bar2', $subdiff->getNew());

        $subdiff = $diff['foo3'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals('bar3', $subdiff->getValue());
        $this->assertEquals('bar3', $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());

        $subdiff = $diff['foo2'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('bar2', $subdiff->getValue());
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
        $this->assertEquals(array('foo2' => array('foo2' => 'bar2'), 'foo3' => 'bar3'), $diff->getValue());
        $this->assertEquals(array('foo1' => 'bar1'), $diff->getOld());
        $this->assertEquals(array('foo2' => array('foo2' => 'bar2'), 'foo3' =>  'bar3'), $diff->getNew());

        $subdiff = $diff['foo1'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals('bar1', $subdiff->getValue());
        $this->assertEquals('bar1', $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());


        $subdiff = $diff['foo2'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array('foo2' => 'bar2'), $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals(array('foo2' => 'bar2'), $subdiff->getNew());

        $subdiff = $diff['foo2']['foo2'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('bar2', $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('bar2', $subdiff->getNew());

        $subdiff = $diff['foo3'];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('bar3', $subdiff->getValue());
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
        $this->assertEquals($var2, $diff->getValue());
        $this->assertEquals($var1, $diff->getOld());
        $this->assertEquals($var2, $diff->getNew());

        $subdiff = $diff->scalar;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo1', $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('foo1', $subdiff->getNew());

        $subdiff = $diff->scalars;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array('foo1'), $subdiff->getValue());
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
        $this->assertEquals($var2, $diff->getValue());
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
        $this->assertEquals($var2, $diff->getValue());
        $this->assertEquals($var1, $diff->getOld());
        $this->assertEquals($var2, $diff->getNew());

        $subdiff = $diff->objects;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array($object), $subdiff->getValue());
        $this->assertEquals(array(), $subdiff->getOld());
        $this->assertEquals(array($object), $subdiff->getNew());

        $subdiff = $diff->objects[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals($object, $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals($object, $subdiff->getNew());

        $subdiff = $diff->objects[0]->scalar;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertTrue($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals('foo2', $subdiff->getValue());
        $this->assertEquals(null, $subdiff->getOld());
        $this->assertEquals('foo2', $subdiff->getNew());

        $diff = $engine->compare($var2, $var1);
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $diff);
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
        $this->assertEquals($var1, $diff->getValue());
        $this->assertEquals($var2, $diff->getOld());
        $this->assertEquals($var1, $diff->getNew());

        $subdiff = $diff->objects;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertTrue($subdiff->isModified());
        $this->assertFalse($subdiff->isDeleted());
        $this->assertEquals(array(), $subdiff->getValue());
        $this->assertEquals(array($object), $subdiff->getOld());
        $this->assertEquals(array(), $subdiff->getNew());

        $subdiff = $diff->objects[0];
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);
        $this->assertFalse($subdiff->isCreated());
        $this->assertFalse($subdiff->isModified());
        $this->assertTrue($subdiff->isDeleted());
        $this->assertEquals($object, $subdiff->getValue());
        $this->assertEquals($object, $subdiff->getOld());
        $this->assertEquals(null, $subdiff->getNew());
    }

    function testCompareObjectsFiltered()
    {
        $engine = new DiffEngine(array(
            'Pitpit\Component\Diff\Tests\DiffEngineObject' => array(
                'scalar'
            )
        ));

        $var1 = new DiffEngineObject();
        $var2 = new DiffEngineObject();
        $var2->scalar = 'foo1';
        $var2->scalars[] = 'foo1';

        $diff = $engine->compare($var1, $var2);

        $subdiff = $diff->scalar;
        $this->assertInstanceOf('Pitpit\Component\Diff\Diff', $subdiff);

        $this->assertfalse(isset($diff->scalars));
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


    function testCompareObjectTString()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = 'foo';

        $diff = $engine->compare($var1, $var2);

        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isUncomparable());
    }

    function testCompareStringToObject()
    {
        $engine = new DiffEngine();
        $var1 = 'foo';
        $var2 = new DiffEngineObject();

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isUncomparable());
    }

    function testCompareArrayToObject()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = array('foo');

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isUncomparable());
    }

    function testCompareObjectToArray()
    {
        $engine = new DiffEngine();
        $var1 = array('foo');
        $var2 = new DiffEngineObject();

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isUncomparable());
    }


    function testCompareNotSameClass()
    {
        $engine = new DiffEngine();
        $var1 = new DiffEngineObject();
        $var2 = new \StdClass();

        $diff = $engine->compare($var1, $var2);
        $this->assertTrue($diff->isModified());
        $this->assertTrue($diff->isUncomparable());
    }

}

// @codingStandardsIgnoreStart
class DiffEngineObject
{
    public $scalars = array();
    public $scalar;
    public $objects = array();

}
// @codingStandardsIgnoreEnd
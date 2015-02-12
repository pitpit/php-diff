<?php

namespace Pitpit\Diff\Tests;

use Pitpit\Diff\Diff;

class DiffTest extends \PHPUnit_Framework_TestCase
{
    function testGetOldGetNew()
    {
        $diff = new Diff('old', 'new');

        $this->assertEquals('old', $diff->getOld());
        $this->assertEquals('new', $diff->getNew());
        $this->assertFalse($diff->getUnchanged());
        $this->assertFalse($diff->getCreated());
        $this->assertFalse($diff->getModified());
        $this->assertFalse($diff->getDeleted());
    }

    function testGetValue()
    {
        $diff = new Diff('old', 'new');

        $this->assertEquals('new', $diff->getValue());

        $diff = new Diff('old', null);
        $diff->setStatus(Diff::STATUS_DELETED);

        $this->assertEquals('old', $diff->getValue(), 'If variable has been deleted, getValue() returns the old value');
    }

    function testGetUnchanged()
    {
        $diff = new Diff('old', 'new');
        $diff->setStatus(Diff::STATUS_UNCHANGED);

        $this->assertTrue($diff->getUnchanged());
        $this->assertFalse($diff->getCreated());
        $this->assertFalse($diff->getModified());
        $this->assertFalse($diff->getDeleted());
    }

    function testGetCreated()
    {
        $diff = new Diff('old', 'new');
        $diff->setStatus(Diff::STATUS_CREATED);

        $this->assertFalse($diff->getUnchanged());
        $this->assertTrue($diff->getCreated());
        $this->assertFalse($diff->getModified());
        $this->assertFalse($diff->getDeleted());
    }

    function testGetModified()
    {
        $diff = new Diff('old', 'new');
        $diff->setStatus(Diff::STATUS_MODIFIED);

        $this->assertFalse($diff->getUnchanged());
        $this->assertFalse($diff->getCreated());
        $this->assertTrue($diff->getModified());
        $this->assertFalse($diff->getDeleted());
    }

    function testGetDeleted()
    {
        $diff = new Diff('old', 'new');
        $diff->setStatus(Diff::STATUS_DELETED);

        $this->assertFalse($diff->getUnchanged());
        $this->assertFalse($diff->getCreated());
        $this->assertFalse($diff->getModified());
        $this->assertTrue($diff->getDeleted());
    }
}
<?php

namespace Pitpit\Component\Diff\Tests;

use Pitpit\Component\Diff\Diff;

class DiffTest extends \PHPUnit_Framework_TestCase
{
    function testGetOldGetNew()
    {
        $diff = new Diff('name', 'old', 'new');

        $this->assertEquals('name', $diff->getIdentifier());
        $this->assertEquals('old', $diff->getOld());
        $this->assertEquals('new', $diff->getNew());
        $this->assertFalse($diff->isSame());
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
    }

    function testGetValue()
    {
        $diff = new Diff('name', 'old', 'new');

        $this->assertEquals('new', $diff->getValue());

        $diff = new Diff('name', 'old', null);
        $diff->setStatus(Diff::STATUS_DELETED);

        $this->assertEquals('old', $diff->getValue(), 'If variable has been deleted, getValue() returns the old value');
    }

    function testIsSame()
    {
        $diff = new Diff('name', 'old', 'new');
        $diff->setStatus(Diff::STATUS_SAME);

        $this->assertTrue($diff->isSame());
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
    }

    function testIsCreated()
    {
        $diff = new Diff('name', 'old', 'new');
        $diff->setStatus(Diff::STATUS_CREATED);

        $this->assertFalse($diff->isSame());
        $this->assertTrue($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertFalse($diff->isDeleted());
    }

    function testIsModified()
    {
        $diff = new Diff('name', 'old', 'new');
        $diff->setStatus(Diff::STATUS_MODIFIED);

        $this->assertFalse($diff->isSame());
        $this->assertFalse($diff->isCreated());
        $this->assertTrue($diff->isModified());
        $this->assertFalse($diff->isDeleted());
    }

    function testIsDeleted()
    {
        $diff = new Diff('name', 'old', 'new');
        $diff->setStatus(Diff::STATUS_DELETED);

        $this->assertFalse($diff->isSame());
        $this->assertFalse($diff->isCreated());
        $this->assertFalse($diff->isModified());
        $this->assertTrue($diff->isDeleted());
    }
}
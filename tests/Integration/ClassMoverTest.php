<?php

namespace DTL\ClassMover\Tests\Integration;

use DTL\ClassMover\Tests\Adapter\AdapterTestCase;

class ClassMoverTest extends AdapterTestCase
{
    public function setUp()
    {
        $this->initWorkspace();
        $this->loadProject();
    }

    /**
     * @testdox It moves a class.
     */
    public function testMoveClass()
    {
        $this->markTestSkipped('Not yet');
        $this->createMover()->moveClass(ClassName::fromString('Acme\\Foobar\\Barfoo'), ClassName::fromString('Acme\\Foobar\\Foobar'));
    }

    private function createMover()
    {
        return new ClassMover();
    }
}

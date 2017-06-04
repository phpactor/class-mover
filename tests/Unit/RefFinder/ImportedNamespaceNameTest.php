<?php

namespace DTL\ClassMover\Tests\Unit\RefFinder;

use PHPUnit\Framework\TestCase;
use DTL\ClassMover\RefFinder\ImportedNamespaceName;

class ImportedNamespaceNameTest extends TestCase
{
    /**
     * @testdox It show replace the head.
     */
    public function testWithAlias()
    {
        $imported = ImportedNamespaceName::fromString('Foobar\\Barfoo\\FooFoo');
        $imported = $imported->withAlias('BarBar');
        $this->assertEquals('Foobar\\Barfoo\\FooFoo', $imported->__toString());
    }

    /**
     * @testdox It allows single part namespace.
     */
    public function testSinglePart()
    {
        $imported = ImportedNamespaceName::fromString('Foobar');
        $this->assertEquals('Foobar', $imported->__toString());
    }

    /**
     * @testdox It does not allow empty namespace.
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Name cannot be empty
     */
    public function testEmpty()
    {
        ImportedNamespaceName::fromString('');
    }
}

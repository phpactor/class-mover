<?php

namespace Phpactor\ClassMover\Tests\Unit\Domain\Name;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\Name\ImportedName;

class ImportedNamespaceNameTest extends TestCase
{
    /**
     * @testdox It show replace the head.
     */
    public function testWithAlias()
    {
        $imported = ImportedName::fromString('Foobar\\Barfoo\\FooFoo');
        $imported = $imported->withAlias('BarBar');
        $this->assertEquals('Foobar\\Barfoo\\FooFoo', $imported->__toString());
    }

    /**
     * @testdox It allows single part namespace.
     */
    public function testSinglePart()
    {
        $imported = ImportedName::fromString('Foobar');
        $this->assertEquals('Foobar', $imported->__toString());
    }

    /**
     * @testdox It does not allow empty namespace.
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Name cannot be empty
     */
    public function testEmpty()
    {
        ImportedName::fromString('');
    }
}

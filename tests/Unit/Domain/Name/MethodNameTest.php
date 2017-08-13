<?php

namespace Phpactor\ClassMover\Tests\Unit\Domain\Name;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\Name\MethodName;

class MethodNameTest extends TestCase
{
    /**
     * @testdox It throws exception on an invalid name
     */
    public function testInvalidName()
    {
        $this->expectException(\InvalidArgumentException::class);
        MethodName::fromString('foobar()');
    }

    public function testValidName()
    {
        $name = MethodName::fromString('foobar');
        $this->assertEquals('foobar', (string) $name);
    }
}

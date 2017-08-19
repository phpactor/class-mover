<?php

namespace Phpactor\ClassMover\Tests\Unit\Domain\Name;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\Name\MethodName;

class MethodNameTest extends TestCase
{
    public function testValidName()
    {
        $name = MethodName::fromString('foobar');
        $this->assertEquals('foobar', (string) $name);
    }
}

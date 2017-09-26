<?php

namespace Phpactor\ClassMover\Tests\Unit\Domain\Name;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\Name\MemberName;

class MemberNameTest extends TestCase
{
    public function testValidName()
    {
        $name = MemberName::fromString('foobar');
        $this->assertEquals('foobar', (string) $name);
    }
}

<?php

namespace Phpactor\ClassMover\Tests\Unit\RefFinder;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\QualifiedName;

class QualifiedNameTest extends TestCase
{
    /**
     * It can say if it is equal to another namespace.
     */
    public function testEqualTo()
    {
        $name = QualifiedName::fromString('Foo\\Bar');
        $notMatching = QualifiedName::fromString('Bar\\Bar');
        $matching = QualifiedName::fromString('Foo\\Bar');

        $this->assertFalse($name->isEqualTo($notMatching));
        $this->assertTrue($name->isEqualTo($matching));
    }
}

<?php

namespace Phpactor\ClassMover\Tests\Unit\RefFinder;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\ClassReference;
use Phpactor\ClassMover\Domain\QualifiedName;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Position;
use Phpactor\ClassMover\Domain\Namespace_;
use Phpactor\ClassMover\Domain\NamespaceReference;
use Phpactor\ClassMover\Domain\ImportedNameReference;

class NamespacedClassRefListTest extends TestCase
{
    /**
     * It should filter for name.
     */
    public function testFilterForName()
    {
        $refList = NamespacedClassReferences::fromNamespaceAndClassRefs(
            NamespaceReference::fromNameAndPosition(Namespace_::fromString('Foo'), Position::fromStartAndEnd(1,2)),
            [
                ClassReference::fromNameAndPosition(
                    QualifiedName::fromString('Foo'),
                    FullyQualifiedName::fromString('Foo\\Bar'),
                    Position::fromStartAndEnd(10, 12),
                    ImportedNameReference::none()
                ),
                ClassReference::fromNameAndPosition(
                    QualifiedName::fromString('Foo'),
                    FullyQualifiedName::fromString('Foo\\Bar'),
                    Position::fromStartAndEnd(10, 12),
                    ImportedNameReference::none()
                ),
                ClassReference::fromNameAndPosition(
                    QualifiedName::fromString('Bar'),
                    FullyQualifiedName::fromString('Bar\\Bar'),
                    Position::fromStartAndEnd(10, 12),
                    ImportedNameReference::none()
                ),
            ]
        );

        $this->assertCount(2, $refList->filterForName(
            FullyQualifiedName::fromString('Foo\\Bar')
        ));

    }
}

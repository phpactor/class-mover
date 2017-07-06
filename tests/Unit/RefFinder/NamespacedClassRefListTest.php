<?php

namespace Phpactor\ClassMover\Tests\Unit\RefFinder;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\NamespacedClassRefList;
use Phpactor\ClassMover\Domain\ClassRef;
use Phpactor\ClassMover\Domain\QualifiedName;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Position;
use Phpactor\ClassMover\Domain\SourceNamespace;
use Phpactor\ClassMover\Domain\NamespaceRef;
use Phpactor\ClassMover\Domain\ImportedNameRef;

class NamespacedClassRefListTest extends TestCase
{
    /**
     * It should filter for name.
     */
    public function testFilterForName()
    {
        $refList = NamespacedClassRefList::fromNamespaceAndClassRefs(
            NamespaceRef::fromNameAndPosition(SourceNamespace::fromString('Foo'), Position::fromStartAndEnd(1,2)),
            [
                ClassRef::fromNameAndPosition(
                    QualifiedName::fromString('Foo'),
                    FullyQualifiedName::fromString('Foo\\Bar'),
                    Position::fromStartAndEnd(10, 12),
                    ImportedNameRef::none()
                ),
                ClassRef::fromNameAndPosition(
                    QualifiedName::fromString('Foo'),
                    FullyQualifiedName::fromString('Foo\\Bar'),
                    Position::fromStartAndEnd(10, 12),
                    ImportedNameRef::none()
                ),
                ClassRef::fromNameAndPosition(
                    QualifiedName::fromString('Bar'),
                    FullyQualifiedName::fromString('Bar\\Bar'),
                    Position::fromStartAndEnd(10, 12),
                    ImportedNameRef::none()
                ),
            ]
        );

        $this->assertCount(2, $refList->filterForName(
            FullyQualifiedName::fromString('Foo\\Bar')
        ));

    }
}

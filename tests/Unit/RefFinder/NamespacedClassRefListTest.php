<?php

namespace DTL\ClassMover\Tests\Unit\RefFinder;

use PHPUnit\Framework\TestCase;
use DTL\ClassMover\Domain\NamespacedClassRefList;
use DTL\ClassMover\Domain\ClassRef;
use DTL\ClassMover\Domain\QualifiedName;
use DTL\ClassMover\Domain\FullyQualifiedName;
use DTL\ClassMover\Domain\Position;
use DTL\ClassMover\Finder\FilePath;
use DTL\ClassMover\Domain\SourceNamespace;
use DTL\ClassMover\Domain\NamespaceRef;
use DTL\ClassMover\Domain\ImportedNameRef;

class NamespacedClassRefListTest extends TestCase
{
    /**
     * It should filter for name.
     */
    public function testFilterForName()
    {
        $refList = NamespacedClassRefList::fromNamespaceAndClassRefs(
            NamespaceRef::fromNameAndPosition(SourceNamespace::fromString('Foo'), Position::fromStartAndEnd(1,2)),
            FilePath::none(),
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

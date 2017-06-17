<?php

namespace DTL\ClassMover\Tests\Unit\RefFinder;

use PHPUnit\Framework\TestCase;
use DTL\ClassMover\RefFinder\NamespacedClassRefList;
use DTL\ClassMover\RefFinder\ClassRef;
use DTL\ClassMover\RefFinder\QualifiedName;
use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\Position;
use DTL\ClassMover\Finder\FilePath;
use DTL\ClassMover\RefFinder\SourceNamespace;

class NamespacedClassRefListTest extends TestCase
{
    /**
     * It should filter for name.
     */
    public function testFilterForName()
    {
        $refList = NamespacedClassRefList::fromNamespaceAndClassRefs(
            SourceNamespace::fromString('Foo'),
            FilePath::none(),
            [
                ClassRef::fromNameAndPosition(
                    QualifiedName::fromString('Foo'),
                    FullyQualifiedName::fromString('Foo\\Bar'),
                    Position::fromStartAndEnd(10, 12)
                ),
                ClassRef::fromNameAndPosition(
                    QualifiedName::fromString('Foo'),
                    FullyQualifiedName::fromString('Foo\\Bar'),
                    Position::fromStartAndEnd(10, 12)
                ),
                ClassRef::fromNameAndPosition(
                    QualifiedName::fromString('Bar'),
                    FullyQualifiedName::fromString('Bar\\Bar'),
                    Position::fromStartAndEnd(10, 12)
                ),
            ]
        );

        $this->assertCount(2, $refList->filterForName(
            FullyQualifiedName::fromString('Foo\\Bar')
        ));

    }
}

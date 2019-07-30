<?php

namespace Phpactor\ClassMover\Tests\Unit\Domain\Reference;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\Model\ReferenceType;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Name\Namespace_;
use Phpactor\ClassMover\Domain\Name\QualifiedName;
use Phpactor\ClassMover\Domain\Reference\ClassReference;
use Phpactor\ClassMover\Domain\Reference\ImportedNameReference;
use Phpactor\ClassMover\Domain\Reference\NamespaceReference;
use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\Reference\Position;

class NamespacedClassReferencesTest extends TestCase
{
    public function testByReferenceTypes()
    {
        $refs = NamespacedClassReferences::fromNamespaceAndClassRefs(
            NamespaceReference::fromNameAndPosition(
                Namespace_::fromString('Foo'),
                Position::fromStartAndEnd(1, 2)
            ),
            [
                $this->createClassReferenceWithType(ReferenceType::CLASS()),
                $this->createClassReferenceWithType(ReferenceType::INTERFACE()),
                $this->createClassReferenceWithType(ReferenceType::TRAIT()),
                $this->createClassReferenceWithType(ReferenceType::CLASS()),
            ]
        );

        $this->assertCount(2, $refs->filterForReferenceTypeIn(ReferenceType::CLASS()));
    }

    private function createClassReferenceWithType(ReferenceType $referenceType)
    {
        return ClassReference::fromNameAndPosition(
            QualifiedName::fromString('Foo'),
            FullyQualifiedName::fromString('Foo\\Bar'),
            Position::fromStartAndEnd(10, 12),
            ImportedNameReference::none(),
            $referenceType
        );
    }
}

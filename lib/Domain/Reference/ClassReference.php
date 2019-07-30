<?php

namespace Phpactor\ClassMover\Domain\Reference;

use Phpactor\ClassMover\Domain\Model\ReferenceType;
use Phpactor\ClassMover\Domain\Name\QualifiedName;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;

final class ClassReference
{
    private $position;
    private $fullName;
    private $name;
    private $isClassDeclaration;
    private $importedNameRef;
    private $referenceType;

    private function __construct(
        QualifiedName $name,
        FullyQualifiedName $fullName,
        Position $position,
        ImportedNameReference $importedNameRef,
        ReferenceType $referenceType,
        bool $isClassDeclaration = false
    ) {
        $this->name = $name;
        $this->fullName = $fullName;
        $this->position = $position;
        $this->importedNameRef = $importedNameRef;
        $this->referenceType = $referenceType;
        $this->isClassDeclaration = $isClassDeclaration;
    }

    public static function fromNameAndPosition(
        QualifiedName $referencedName,
        FullyQualifiedName $fullName,
        Position $position,
        ImportedNameReference $importedNameRef,
        ReferenceType $referenceType,
        bool $isClassDeclaration = false
    ) {
        return new self($referencedName, $fullName, $position, $importedNameRef, $referenceType, $isClassDeclaration);
    }

    public function __toString()
    {
        return (string) $this->fullName;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function name(): QualifiedName
    {
        return $this->name;
    }

    public function fullName(): FullyQualifiedName
    {
        return $this->fullName;
    }

    public function importedNameRef(): ImportedNameReference
    {
        return $this->importedNameRef;
    }

    public function isClassDeclaration(): bool
    {
        return $this->isClassDeclaration;
    }

    public function referenceType(): ReferenceType
    {
        return $this->referenceType;
    }
}

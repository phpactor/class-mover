<?php

namespace Phpactor\ClassMover\Domain;

final class ClassRef
{
    private $position;
    private $fullName;
    private $name;
    private $isClassDeclaration;
    private $importedNameRef;

    public static function fromNameAndPosition(
        QualifiedName $referencedName,
        FullyQualifiedName $fullName,
        Position $position,
        ImportedNameRef $importedNameRef,
        bool $isClassDeclaration = false
    ) {
        $new = new self();
        $new->position = $position;
        $new->name = $referencedName;
        $new->fullName = $fullName;
        $new->importedNameRef = $importedNameRef;
        $new->isClassDeclaration = $isClassDeclaration;

        return $new;
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

    public function importedNameRef(): ImportedNameRef
    {
        return $this->importedNameRef;
    }

    public function isClassDeclaration(): bool
    {
        return $this->isClassDeclaration;
    }
}

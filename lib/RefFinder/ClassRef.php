<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\Position;
use DTL\ClassMover\RefFinder\QualifiedName;

final class ClassRef
{
    private $position;
    private $fullName;
    private $name;
    private $isClassDeclaration;

    public static function fromNameAndPosition(QualifiedName $referencedName, FullyQualifiedName $fullName, Position $position, bool $isClassDeclaration = false)
    {
        $new = new self();
        $new->position = $position;
        $new->name = $referencedName;
        $new->fullName = $fullName;
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

    public function isClassDeclaration(): bool
    {
        return $this->isClassDeclaration;
    }
}

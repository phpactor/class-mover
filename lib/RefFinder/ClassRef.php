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

    public static function fromNameAndPosition(QualifiedName $qualifiedName, FullyQualifiedName $fullName, Position $position)
    {
        $new = new self();
        $new->position = $position;
        $new->name = $qualifiedName;
        $new->fullName = $fullName;

        return $new;
    }

    public function __toString()
    {
        return (string) $this->fullName;
    }

    public function position()
    {
        return $this->position;
    }
}

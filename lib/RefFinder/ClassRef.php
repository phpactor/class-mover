<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\Position;

final class ClassRef
{
    private $position;
    private $name;

    public static function fromNameAndPosition(FullyQualifiedName $name, pOSition $position)
    {
        $new = new self();
        $new->position = $position;
        $new->name = $name;

        return $new;
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    public function position()
    {
        return $this->position;
    }
}

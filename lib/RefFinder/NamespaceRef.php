<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\Position;

final class NamespaceRef
{
    private $position;
    private $namespace;

    public static function fromNameAndPosition(SourceNamespace $namespace, Position $position)
    {
        $new = new self();
        $new->position = $position;
        $new->namespace = $namespace;

        return $new;
    }

    public static function forRoot()
    {
        $new = new self();
        $new->namespace = SourceNamespace::root();

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

    public function namespace()
    {
        return $this->namespace;
    }
}

<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Position;

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

<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Position;
use Phpactor\ClassMover\Domain\Namespace_;

final class NamespaceReference
{
    private $position;
    private $namespace;

    public static function fromNameAndPosition(Namespace_ $namespace, Position $position)
    {
        $new = new self();
        $new->position = $position;
        $new->namespace = $namespace;

        return $new;
    }

    public static function forRoot()
    {
        $new = new self();
        $new->namespace = Namespace_::root();

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

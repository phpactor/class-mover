<?php

namespace Phpactor\ClassMover\Domain\Reference;

use Phpactor\ClassMover\Domain\Name\Namespace_;
use Phpactor\ClassMover\Domain\Reference\Position;
use PHPUnit_Framework_MockObject_Builder_Namespace;

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

    public static function forRoot(): NamespaceReference
    {
        $new = new self();
        $new->namespace = Namespace_::root();

        return $new;
    }

    public function __toString()
    {
        return (string) $this->namespace;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function namespace(): Namespace_
    {
        return $this->namespace;
    }
}

<?php

namespace Phpactor\ClassMover\Domain\Model;

use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;

class Class_
{
    /**
     * @var FullyQualifiedName
     */
    private $name;

    private function __construct(FullyQualifiedName $name)
    {
        $this->name = $name;
    }

    public function fromFullyQualifiedName(FullyQualifiedName $name)
    {
        return new self($name);
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}

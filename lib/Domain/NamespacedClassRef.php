<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\ClassRef;
use Phpactor\ClassMover\Domain\NamespaceRef;

class NamespacedClassRef
{
    private $namespaceRef;
    private $classRef;

    private function __construct(NamespaceRef $namespaceRef, ClassRef $classRef)
    {
        $this->namespaceRef = $namespaceRef;
        $this->classRef = $classRef;
    }

    public static function fromNamespaceRefAndClassRef(NamespaceRef $namespaceRef, ClassRef $classRef)
    {
        return new self($namespaceRef, $classRef);
    }

    public function classRef(): ClassRef
    {
        return $this->classRef;
    }

    public function namespaceRef(): NamespaceRef
    {
        return $this->namespaceRef;
    }
}

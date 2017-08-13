<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\ClassReference;
use Phpactor\ClassMover\Domain\NamespaceReference;

class NamespacedClassReference
{
    private $namespaceRef;
    private $classRef;

    private function __construct(NamespaceReference $namespaceRef, ClassReference $classRef)
    {
        $this->namespaceRef = $namespaceRef;
        $this->classRef = $classRef;
    }

    public static function fromNamespaceRefAndClassRef(NamespaceReference $namespaceRef, ClassReference $classRef)
    {
        return new self($namespaceRef, $classRef);
    }

    public function classRef(): ClassReference
    {
        return $this->classRef;
    }

    public function namespaceRef(): NamespaceReference
    {
        return $this->namespaceRef;
    }
}

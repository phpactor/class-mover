<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\ClassRef;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Phpactor\ClassMover\Domain\NamespaceRef;

final class NamespacedClassRefList implements \IteratorAggregate
{
    private $classRefs = array();
    private $namespaceRef;

    private function __construct(NamespaceRef $namespaceRef, array $classRefs)
    {
        $this->namespaceRef = $namespaceRef;
        foreach ($classRefs as $classRef) {
            $this->add($classRef);
        }
    }

    public static function fromNamespaceAndClassRefs(NamespaceRef $namespace, array $classRefs)
    {
        return new self($namespace, $classRefs);
    }

    public static function empty()
    {
        return new self(NamespaceRef::forRoot(), []);
    }

    public function filterForName(FullyQualifiedName $name): NamespacedClassRefList
    {
        return new self($this->namespaceRef, array_filter($this->classRefs, function (ClassRef $classRef) use ($name) {
            return $classRef->fullName()->isEqualTo($name);
        }));
    }

    public function isEmpty(): bool
    {
        return empty($this->classRefs);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->classRefs);
    }

    public function namespaceRef(): NamespaceRef
    {
        return $this->namespaceRef;
    }

    private function add(ClassRef $classRef)
    {
        $this->classRefs[] = $classRef;
    }
}

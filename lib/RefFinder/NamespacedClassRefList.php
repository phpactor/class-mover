<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\Finder\FilePath;

final class NamespacedClassRefList implements \IteratorAggregate
{
    private $classRefs = array();
    private $path;
    private $namespaceRef;

    private function __construct(NamespaceRef $namespaceRef, FilePath $path, array $classRefs)
    {
        $this->namespaceRef = $namespaceRef;
        $this->path = $path;
        foreach ($classRefs as $classRef) {
            $this->add($classRef);
        }
    }

    public static function fromNamespaceAndClassRefs(NamespaceRef $namespace, FilePath $path, array $classRefs)
    {
        return new self($namespace, $path, $classRefs);
    }

    public function filterForName(FullyQualifiedName $name)
    {
        return new self($this->namespaceRef, $this->path, array_filter($this->classRefs, function (ClassRef $classRef) use ($name) {
            return $classRef->fullName()->isEqualTo($name);
        }));
    }

    public function path(): FilePath
    {
        return $this->path;
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

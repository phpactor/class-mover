<?php

namespace Phpactor\ClassMover\Domain\Reference;

final class MethodReferences implements \IteratorAggregate
{
    private $methodReferences = [];

    private function __construct($methodReferences)
    {
        foreach ($methodReferences as $item) {
            $this->add($item);
        }
    }

    public static function fromMethodReferences(array $methodReferences): MethodReferences
    {
         return new self($methodReferences);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->methodReferences);
    }

    private function add(MethodReference $item)
    {
        $this->methodReferences[] = $item;
    }
}

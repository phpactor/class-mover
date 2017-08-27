<?php

namespace Phpactor\ClassMover\Domain\Reference;

final class MethodReferences implements \IteratorAggregate, \Countable
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

    public function withClasses(): MethodReferences
    {
        return self::fromMethodReferences(array_filter($this->methodReferences, function (MethodReference $reference) {
            return $reference->hasClass();
        }));
    }

    public function withoutClasses(): MethodReferences
    {
        return self::fromMethodReferences(array_filter($this->methodReferences, function (MethodReference $reference) {
            return false === $reference->hasClass();
        }));
    }

    private function add(MethodReference $item)
    {
        $this->methodReferences[] = $item;
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->methodReferences);
    }
}


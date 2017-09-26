<?php

namespace Phpactor\ClassMover\Domain\Reference;

use Phpactor\ClassMover\Domain\Reference\MemberReference;
use Phpactor\ClassMover\Domain\Reference\MemberReferences;

final class MemberReferences implements \IteratorAggregate, \Countable
{
    private $methodReferences = [];

    private function __construct($methodReferences)
    {
        foreach ($methodReferences as $item) {
            $this->add($item);
        }
    }

    public static function fromMethodReferences(array $methodReferences): MemberReferences
    {
         return new self($methodReferences);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->methodReferences);
    }

    public function withClasses(): MemberReferences
    {
        return self::fromMethodReferences(array_filter($this->methodReferences, function (MemberReference $reference) {
            return $reference->hasClass();
        }));
    }

    public function withoutClasses(): MemberReferences
    {
        return self::fromMethodReferences(array_filter($this->methodReferences, function (MemberReference $reference) {
            return false === $reference->hasClass();
        }));
    }

    private function add(MemberReference $item)
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


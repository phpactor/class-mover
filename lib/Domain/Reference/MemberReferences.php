<?php

namespace Phpactor\ClassMover\Domain\Reference;

final class MemberReferences implements \IteratorAggregate, \Countable
{
    private $methodReferences = [];

    private function __construct($methodReferences)
    {
        foreach ($methodReferences as $item) {
            $this->add($item);
        }
    }

    public static function fromMemberReferences(array $methodReferences): MemberReferences
    {
        return new self($methodReferences);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->methodReferences);
    }

    public function withClasses(): MemberReferences
    {
        return self::fromMemberReferences(array_filter($this->methodReferences, function (MemberReference $reference) {
            return $reference->hasClass();
        }));
    }

    public function withoutClasses(): MemberReferences
    {
        return self::fromMemberReferences(array_filter($this->methodReferences, function (MemberReference $reference) {
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

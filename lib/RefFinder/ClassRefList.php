<?php

namespace DTL\ClassMover\RefFinder;

final class ClassRefList implements \IteratorAggregate
{
    private $names;

    public static function fromFullyQualifiedNames(array $names)
    {
        $new = new self();
        foreach ($names as $name) {
            $new->add($name);
        }

        return $new;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->names);
    }

    private function add(FullyQualifiedName $name)
    {
        $this->names[] = $name;
    }
}

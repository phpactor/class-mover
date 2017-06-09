<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\ClassRef;
use DTL\ClassMover\Finder\FilePath;

final class ClassRefList implements \IteratorAggregate
{
    private $classRefs = array();
    private $path;

    public static function fromClassRefs(FilePath $path, array $classRefs)
    {
        $new = new self();
        $new->path = $path;
        foreach ($classRefs as $classRef) {
            $new->add($classRef);
        }

        return $new;
    }

    public function path(): FilePath
    {
        return $this->path;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->classRefs);
    }

    private function add(ClassRef $classRef)
    {
        $this->classRefs[] = $classRef;
    }
}

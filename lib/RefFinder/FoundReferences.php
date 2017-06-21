<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\NamespacedClassRefList;

final class FoundReferences
{
    private $source;
    private $name;
    private $references;

    public function __construct(FileSource $source, FullyQualifiedName $name, NamespacedClassRefList $list)
    {
        $this->source = $source;
        $this->name = $name;
        $this->references = $list;
    }

    public function source(): FileSource
    {
        return $this->source;
    }

    public function targetName(): FullyQualifiedName
    {
        return $this->name;
    }

    public function references(): NamespacedClassRefList
    {
        return $this->references;
    }
}

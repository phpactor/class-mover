<?php

namespace DTL\ClassMover\Domain;

use DTL\ClassMover\Domain\SourceCode;
use DTL\ClassMover\Domain\FullyQualifiedName;
use DTL\ClassMover\Domain\NamespacedClassRefList;

final class FoundReferences
{
    private $source;
    private $name;
    private $references;

    public function __construct(SourceCode $source, FullyQualifiedName $name, NamespacedClassRefList $list)
    {
        $this->source = $source;
        $this->name = $name;
        $this->references = $list;
    }

    public function source(): SourceCode
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

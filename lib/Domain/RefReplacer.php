<?php

namespace DTL\ClassMover\Domain;

use DTL\ClassMover\Domain\SourceCode;
use DTL\ClassMover\Domain\FullyQualifiedName;
use DTL\ClassMover\Domain\NamespacedClassRefList;

interface RefReplacer
{
    public function replaceReferences(SourceCode $source, NamespacedClassRefList $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName): SourceCode;
}

<?php

namespace DTL\ClassMover\Domain;

use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\Domain\FullyQualifiedName;
use DTL\ClassMover\Domain\NamespacedClassRefList;

interface RefReplacer
{
    public function replaceReferences(FileSource $source, NamespacedClassRefList $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName);
}

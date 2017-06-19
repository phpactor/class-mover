<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\Finder\FileSource;

interface RefReplacer
{
    public function replaceReferences(FileSource $source, NamespacedClassRefList $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName);
}

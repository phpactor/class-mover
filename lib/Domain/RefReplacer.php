<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Phpactor\ClassMover\Domain\NamespacedClassRefList;

interface RefReplacer
{
    public function replaceReferences(SourceCode $source, NamespacedClassRefList $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName): SourceCode;
}

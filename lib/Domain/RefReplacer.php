<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Phpactor\ClassMover\Domain\NamespacedClassReferences;

interface RefReplacer
{
    public function replaceReferences(SourceCode $source, NamespacedClassReferences $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName): SourceCode;
}

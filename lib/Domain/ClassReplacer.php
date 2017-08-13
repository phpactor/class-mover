<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;

interface ClassReplacer
{
    public function replaceReferences(SourceCode $source, NamespacedClassReferences $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName): SourceCode;
}

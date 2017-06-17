<?php

namespace DTL\ClassMover\RefFinder;

interface RefReplacer 
{
    public function replaceReferences(FullyQualifiedName $originalName, FullyQualifiedName $newName, NamespacedClassRefList $refList);
}

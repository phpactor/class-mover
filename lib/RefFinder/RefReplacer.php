<?php

namespace DTL\ClassMover\RefFinder;

interface RefReplacer 
{
    public function replaceReferences(FullyQualifiedName $name, NamespacedClassRefList $refList);
}

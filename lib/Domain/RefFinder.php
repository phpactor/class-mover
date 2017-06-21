<?php

namespace DTL\ClassMover\Domain;



use DTL\ClassMover\Domain\SourceCode;
use DTL\ClassMover\Domain\NamespacedClassRefList;

interface RefFinder
{
    public function findIn(SourceCode $source): NamespacedClassRefList;
}

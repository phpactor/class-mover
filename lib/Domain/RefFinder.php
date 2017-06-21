<?php

namespace DTL\ClassMover\Domain;



use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\Domain\NamespacedClassRefList;

interface RefFinder
{
    public function findIn(FileSource $source): NamespacedClassRefList;
}

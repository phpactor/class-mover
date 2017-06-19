<?php

namespace DTL\ClassMover\RefFinder;



use DTL\ClassMover\Finder\FileSource;

interface RefFinder
{
    public function findIn(FileSource $source): NamespacedClassRefList;
}

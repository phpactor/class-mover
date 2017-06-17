<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\ClassRefList;
use DTL\ClassFileConverter\ClassName;
use DTL\ClassMover\Finder\FilePath;
use DTL\ClassMover\Finder\FileSource;

interface RefFinder
{
    public function findIn(FileSource $source): NamespacedClassRefList;
}

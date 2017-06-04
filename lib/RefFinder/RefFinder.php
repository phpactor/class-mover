<?php

namespace DTL\ClassMover\RefFinder;

interface RefFinder
{
    public function findRefencesIn(FilePath $file, ClassName $className): ClassReferences;
}

<?php

namespace Phpactor\ClassMover\Domain;



use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\NamespacedClassReferences;

interface RefFinder
{
    public function findIn(SourceCode $source): NamespacedClassReferences;
}

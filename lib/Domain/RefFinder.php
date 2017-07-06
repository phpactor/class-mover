<?php

namespace Phpactor\ClassMover\Domain;



use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\NamespacedClassRefList;

interface RefFinder
{
    public function findIn(SourceCode $source): NamespacedClassRefList;
}

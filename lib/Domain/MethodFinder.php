<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Model\ClassMethod;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;

interface MethodFinder
{
    public function findMethods(SourceCode $source, ClassMethod $methodMethod): MethodReferences;
}

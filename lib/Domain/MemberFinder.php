<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;

interface MemberFinder
{
    public function findMethods(SourceCode $source, ClassMethodQuery $methodMethod): MethodReferences;
}

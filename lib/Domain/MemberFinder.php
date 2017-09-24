<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;

interface MemberFinder
{
    public function findMembers(SourceCode $source, ClassMethodQuery $methodMethod): MethodReferences;
}

<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;
use Phpactor\ClassMover\Domain\SourceCode;

interface MemberReplacer
{
    public function replaceMethods(SourceCode $source, MethodReferences $references, string $newName): SourceCode;
}

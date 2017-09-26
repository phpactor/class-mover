<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Model\ClassMemberQuery;
use Phpactor\ClassMover\Domain\Reference\MemberReferences;
use Phpactor\ClassMover\Domain\SourceCode;

interface MethodReplacer
{
    public function replaceMethods(SourceCode $source, MemberReferences $references, string $newName): SourceCode;
}

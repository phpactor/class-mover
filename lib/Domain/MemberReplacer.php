<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Model\ClassMemberQuery;
use Phpactor\ClassMover\Domain\Reference\MemberReferences;
use Phpactor\ClassMover\Domain\SourceCode;

interface MemberReplacer
{
    public function replaceMembers(SourceCode $source, MemberReferences $references, string $newName): SourceCode;
}

<?php

namespace Phpactor\ClassMover\Adapter\WorseTolerant;

use Microsoft\PhpParser\TextEdit;

use Phpactor\ClassMover\Domain\ClassReplacer;
use Phpactor\ClassMover\Domain\MemberReplacer;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Reference\ImportedNameReference;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;
use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Reference\MethodReference;

class WorseTolerantMemberReplacer implements MemberReplacer
{
    public function replaceMethods(SourceCode $source, MethodReferences $references, string $newName): SourceCode
    {
        $edits = [];
        /** @var $reference MethodReference */
        foreach ($references as $reference) {
            $edits[] = new TextEdit($reference->position()->start(), $reference->position()->length(), $newName);
        }

        $source = $source->replaceSource(TextEdit::applyEdits($edits, $source->__toString()));

        return $source;
    }
}


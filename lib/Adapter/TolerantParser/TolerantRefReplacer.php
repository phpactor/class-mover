<?php

namespace Phpactor\ClassMover\Adapter\TolerantParser;

use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Microsoft\PhpParser\TextEdit;
use Phpactor\ClassMover\Domain\NamespacedClassRefList;
use Phpactor\ClassMover\Domain\RefReplacer;
use Phpactor\ClassMover\Domain\ImportedNameRef;

class TolerantRefReplacer implements RefReplacer
{
    public function replaceReferences(
        SourceCode $source,
        NamespacedClassRefList $classRefList,
        FullyQualifiedName $originalName,
        FullyQualifiedName $newName
    ): SourceCode
    {
        $edits = [];
        $addUse = false;

        foreach ($classRefList as $classRef) {
            if (
                ImportedNameRef::none() == $classRef->importedNameRef() &&
                false === ($classRef->isClassDeclaration() && $classRef->fullName()->equals($originalName))
            ) {
                $addUse = true;
            }

            if ($classRef->isClassDeclaration() && $classRef->fullName()->equals($originalName)) {
                $edits[] = new TextEdit(
                    $classRefList->namespaceRef()->position()->start(),
                    $classRefList->namespaceRef()->position()->length(),
                    $newName->parentNamespace()->__toString()
                );
            }

            $edits[] = new TextEdit(
                $classRef->position()->start(),
                $classRef->position()->length(),
                $classRef->name()->transpose($newName)->__toString()
            );
        }

        $source = $source->replaceSource(TextEdit::applyEdits($edits, $source->__toString()));
        if (true === $addUse) {
            $source = $source->addUseStatement($newName);
        }

        return $source;
    }
}

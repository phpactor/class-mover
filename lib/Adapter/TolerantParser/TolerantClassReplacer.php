<?php

namespace Phpactor\ClassMover\Adapter\TolerantParser;

use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Microsoft\PhpParser\TextEdit;
use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\ClassReplacer;
use Phpactor\ClassMover\Domain\Reference\ImportedNameReference;

class TolerantClassReplacer implements ClassReplacer
{
    public function replaceReferences(
        SourceCode $source,
        NamespacedClassReferences $classRefList,
        FullyQualifiedName $originalName,
        FullyQualifiedName $newName
    ): SourceCode {
        $edits = [];
        $addUse = false;

        foreach ($classRefList as $classRef) {
            if (
                ImportedNameReference::none() == $classRef->importedNameRef() &&
                false === ($classRef->isClassDeclaration() && $classRef->fullName()->equals($originalName))
            ) {
                $addUse = true;
            }

            // if the class is the original instance, change its namespace
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

        // make sure the edits are ordered
        usort($edits, function (TextEdit $a, TextEdit $b) {
            return $a->start <=> $b->start;
        });

        $source = $source->replaceSource(TextEdit::applyEdits($edits, $source->__toString()));
        if (true === $addUse) {
            $source = $source->addUseStatement($newName);
        }

        return $source;
    }
}

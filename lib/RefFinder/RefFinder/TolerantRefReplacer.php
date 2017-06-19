<?php

namespace DTL\ClassMover\RefFinder\RefFinder;

use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\RefFinder\ClassRefList;
use DTL\ClassMover\RefFinder\FullyQualifiedName;
use Microsoft\PhpParser\TextEdit;
use DTL\ClassMover\RefFinder\NamespacedClassRefList;
use DTL\ClassMover\RefFinder\RefReplacer;
use DTL\ClassMover\RefFinder\ImportedNameRef;

class TolerantRefReplacer implements RefReplacer
{
    public function replaceReferences(FileSource $source, NamespacedClassRefList $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName)
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

<?php

namespace DTL\ClassMover\RefFinder\RefFinder;

use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\RefFinder\ClassRefList;
use DTL\ClassMover\RefFinder\FullyQualifiedName;
use Microsoft\PhpParser\TextEdit;
use DTL\ClassMover\RefFinder\NamespacedClassRefList;
use DTL\ClassMover\RefFinder\RefReplacer;

class TolerantRefReplacer implements RefReplacer
{
    public function replaceReferences(FileSource $source, NamespacedClassRefList $classRefList, FullyQualifiedName $originalName, FullyQualifiedName $newName)
    {
        $edits = [];

        if ($classRefList->namespaceRef()->namespace()->equals($originalName->parentNamespace())) {
            $edits[] = new TextEdit(
                $classRefList->namespaceRef()->position()->start(),
                $classRefList->namespaceRef()->position()->length(),
                $newName->parentNamespace()->__toString()
            );
        }

        foreach ($classRefList as $classRef) {
            $edits[] = new TextEdit(
                $classRef->position()->start(),
                $classRef->position()->length(),
                $classRef->name()->transpose($newName)->__toString()
            );
        }

        return $source->replaceSource(TextEdit::applyEdits($edits, $source->__toString()));
    }
}

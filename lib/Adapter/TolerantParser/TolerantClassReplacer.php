<?php

namespace Phpactor\ClassMover\Adapter\TolerantParser;

use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Microsoft\PhpParser\TextEdit;
use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\ClassReplacer;
use Phpactor\ClassMover\Domain\Reference\ImportedNameReference;
use Phpactor\ClassMover\Domain\Reference\ClassReference;

class TolerantClassReplacer implements ClassReplacer
{
    public function replaceReferences(
        SourceCode $source,
        NamespacedClassReferences $classRefList,
        FullyQualifiedName $originalName,
        FullyQualifiedName $newName
    ): SourceCode {
        $edits = [];
        $importClass = false;
        $addNamespace = false;

        foreach ($classRefList as $classRef) {
            $importClass = $this->shouldImportClass($classRef, $originalName);

            if ($this->classIsTheOriginalInstance($classRef, $originalName)) {
                $addNamespace = $classRefList->namespaceRef()->namespace()->isRoot();

                if (false === $addNamespace) {
                    $edits[] = $this->replaceOriginalInstanceNamespace($classRefList, $newName);
                }
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
        if (true === $importClass) {
            $source = $source->addUseStatement($newName);
        }

        if (true === $addNamespace) {
            $source = $source->addNamespace($newName->parentNamespace());
        }

        return $source;
    }

    private function replaceOriginalInstanceNamespace(NamespacedClassReferences $classRefList, FullyQualifiedName $newName)
    {
        return new TextEdit(
            $classRefList->namespaceRef()->position()->start(),
            $classRefList->namespaceRef()->position()->length(),
            $newName->parentNamespace()->__toString()
        );
    }

    private function shouldImportClass(ClassReference $classRef, FullyQualifiedName $originalName)
    {
        return ImportedNameReference::none() == $classRef->importedNameRef() &&
            false === ($classRef->isClassDeclaration() && $classRef->fullName()->equals($originalName));
    }

    private function classIsTheOriginalInstance($classRef, $originalName)
    {
        return $classRef->isClassDeclaration() && $classRef->fullName()->equals($originalName);
    }
}

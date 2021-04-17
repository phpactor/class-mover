<?php

namespace Phpactor\ClassMover\Adapter\TolerantParser;

use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\ClassReplacer;
use Phpactor\ClassMover\Domain\Reference\ImportedNameReference;
use Phpactor\ClassMover\Domain\Reference\ClassReference;
use Phpactor\TextDocument\TextDocument;
use Phpactor\TextDocument\TextEdit;
use Phpactor\TextDocument\TextEdits;

class TolerantClassReplacer implements ClassReplacer
{
    public function replaceReferences(
        TextDocument $source,
        NamespacedClassReferences $classRefList,
        FullyQualifiedName $originalName,
        FullyQualifiedName $newName
    ): TextEdits {
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

            $edits[] = TextEdit::create(
                $classRef->position()->start(),
                $classRef->position()->length(),
                $classRef->name()->transpose($newName)->__toString()
            );
        }

        // make sure the edits are ordered
        usort($edits, function (TextEdit $a, TextEdit $b) {
            return $a->start()->toInt() <=> $b->start()->toInt();
        });

        if (true === $importClass) {
            $edits[] = $this->addUseStatement($source, $newName);
        }

        //if (true === $addNamespace) {
        //    $edits[] = $source->addNamespace($newName->parentNamespace());
        //}

        return TextEdits::fromTextEdits($edits);
    }

    private function replaceOriginalInstanceNamespace(NamespacedClassReferences $classRefList, FullyQualifiedName $newName)
    {
        return TextEdit::create(
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

    private function addUseStatement(FullyQualifiedName $newName, TextDocument $source)
    {
    }
}

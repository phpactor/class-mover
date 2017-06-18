<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\Position;
use DTL\ClassMover\RefFinder\QualifiedName;

final class ImportedNameRef
{
    private $position;
    private $importedName;
    
    public static function fromImportedNameAndPosition(ImportedName $importedName, Position $position)
    {
        $new = new self();
        $new->position = $position;
        $new->importedName = $importedName;

        return $new;
    }

    public static function forRoot()
    {
        $new = new self();
        $new->importedName = SourceNamespace::root();

        return $new;
    }

    public function __toString()
    {
        return (string) $this->fullName;
    }

    public function position()
    {
        return $this->position;
    }

    public function importedName()
    {
        return $this->importedName;
    }
}

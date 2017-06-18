<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\Position;
use DTL\ClassMover\RefFinder\QualifiedName;
use DTL\ClassMover\RefFinder\ImportedName;

final class ImportedNameRef
{
    private $position;
    private $importedName;
    private $exists = true;

    public static function none()
    {
        $new = new self([]);
        $new->exists = false;

        return $new;
    }

    public static function fromImportedNameAndPosition(ImportedName $importedName, Position $position)
    {
        $new = new self();
        $new->position = $position;
        $new->importedName = $importedName;

        return $new;
    }

    public function exists()
    {
        return $this->exists;
    }

    public function __toString()
    {
        return (string) $this->fullName;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function importedName(): ImportedName
    {
        return $this->importedName;
    }
}

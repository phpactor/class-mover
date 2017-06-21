<?php

namespace DTL\ClassMover\Domain;

use DTL\ClassMover\Domain\FullyQualifiedName;
use DTL\ClassMover\Domain\QualifiedName;

class SourceNamespace extends QualifiedName
{
    public function qualify(QualifiedName $name): FullyQualifiedName
    {
        return FullyQualifiedName::fromString($this->__toString().'\\'.$name->__toString());
    }
}

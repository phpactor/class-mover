<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\FullyQualifiedName;

class SourceNamespace extends QualifiedName
{
    public function qualify(QualifiedName $name): FullyQualifiedName
    {
        return FullyQualifiedName::fromString($this->__toString() . '\\' . $name->__toString());
    }
}

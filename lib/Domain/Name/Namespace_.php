<?php

namespace Phpactor\ClassMover\Domain\Name;

use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Name\QualifiedName;

class Namespace_ extends QualifiedName
{
    public function qualify(QualifiedName $name): FullyQualifiedName
    {
        return FullyQualifiedName::fromString($this->__toString().'\\'.$name->__toString());
    }
}

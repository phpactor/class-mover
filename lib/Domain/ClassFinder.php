<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;

interface ClassFinder
{
    public function findIn(SourceCode $source): NamespacedClassReferences;
}

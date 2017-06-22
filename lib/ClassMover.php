<?php

namespace DTL\ClassMover;

use DTL\ClassMover\Domain\FullyQualifiedName;
use DTL\ClassMover\Domain\SourceCode;
use DTL\ClassMover\Domain\RefFinder;
use DTL\ClassMover\Domain\RefReplacer;
use DTL\ClassMover\Domain\FoundReferences;
use DTL\ClassMover\Adapter\TolerantParser\TolerantRefFinder;
use DTL\ClassMover\Adapter\TolerantParser\TolerantRefReplacer;

/**
 * Facade.
 */
class ClassMover
{
    private $finder;
    private $replacer;

    public function __construct(RefFinder $finder = null, RefReplacer $replacer = null)
    {
        $this->finder = $finder ?: new TolerantRefFinder();
        $this->replacer = $replacer ?: new TolerantRefReplacer();
    }

    public function findReferences(string $source, string $fullyQualifiedName): FoundReferences
    {
        $source = SourceCode::fromString($source);
        $name = FullyQualifiedName::fromString($fullyQualifiedName);
        $source = SourceCode::fromString($source);
        $references = $this->finder->findIn($source)->filterForName($name);

        return new FoundReferences($source, $name, $references);
    }

    public function replaceReferences(FoundReferences $foundReferences, string $newFullyQualifiedName): SourceCode
    {
        $newName = FullyQualifiedName::fromString($newFullyQualifiedName);
        return $this->replacer->replaceReferences(
            $foundReferences->source(),
            $foundReferences->references(),
            $foundReferences->targetName(),
            $newName
        );
    }
}

<?php

namespace DTL\ClassMover;

use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\Finder\FilePath;
use DTL\ClassMover\RefFinder\FileSource;

/**
 * Facade.
 */
class ClassMover
{
    private $finder;
    private $replacer;

    public function __construct(RefFinder $finder, RefReplacer $replacer)
    {
        $this->finder = $finder;
        $this->replacer = $replacer;
    }

    public function findReferences(string $source, string $fullyQualifiedName): FoundReferences
    {
        $source = FileSource::fromFilePathAndString(FilePath::none(), $source);
        $name = FullyQualifiedName::fromString($fullyQualifiedName);
        $source = FileSource::fromString($source);
        $references = $this->finder->findIn($source)->filterForName($name);

        return new FoundReferences($source, $name, $references);
    }

    public function replaceReferences(FoundReferences $foundReferences, string $newFullyQualifiedName)
    {
        $newName = FullyQualifiedName::fromString($newFullyQualifiedName);
        $this->replacer->replaceReferences(
            $foundReferences->source(),
            $foundReferences->references(),
            $foundReferences->targetName(),
            $newName
        );
    }
}

<?php

namespace Phpactor\ClassMover;

use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\RefFinder;
use Phpactor\ClassMover\Domain\RefReplacer;
use Phpactor\ClassMover\Domain\FoundReferences;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantRefFinder;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantRefReplacer;

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

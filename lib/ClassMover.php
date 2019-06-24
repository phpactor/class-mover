<?php

namespace Phpactor\ClassMover;

use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\ClassFinder;
use Phpactor\ClassMover\Domain\ClassReplacer;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantClassFinder;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantClassReplacer;

class ClassMover
{
    private $finder;
    private $replacer;

    public function __construct(ClassFinder $finder = null, ClassReplacer $replacer = null)
    {
        $this->finder = $finder ?: new TolerantClassFinder();
        $this->replacer = $replacer ?: new TolerantClassReplacer();
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

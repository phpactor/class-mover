<?php

namespace Phpactor\ClassMover\Adapter\WorseTolerant;

use Phpactor\ClassMover\Domain\MethodFinder;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Model\ClassMethod;
use Phpactor\WorseReflection\Reflector;
use Phpactor\WorseReflection\Core\SourceCodeLocator\StringSourceLocator;
use Microsoft\PhpParser\Parser;

class WorseTolerantMethodFinder implements MethodFinder
{
    /**
     * @var Reflector
     */
    private $reflector;

    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Reflector $reflector = null, Parser $parser = null)
    {
        $this->reflector = $reflector ?: Reflector::create(StringSourceLocator::fromString(''));
        $this->parser = $parser ?: new Parser();
    }

    public function findMethods(SourceCode $source, ClassMethod $methodMethod): MethodReferences
    {
        return MethodReferences::fromMethodReferences([]);
    }

    private function collectionMethodReferences(Node $node, string $sourceCode, ClassMethod $method)
    {
    }
}


<?php

namespace DTL\ClassMover\RefFinder\RefFinder;

use Microsoft\PhpParser\Parser;
use DTL\ClassMover\Finder\FileSource;
use Microsoft\PhpParser\Node\Statement\NamespaceDefinition;
use Microsoft\PhpParser\Node\Statement\NamespaceUseDeclaration;
use Microsoft\PhpParser\Node\SourceFileNode;

class TolerantRefFinder
{
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function findIn(FileSource $source): ClassRefList
    {
        $ast = $this->parser->parseSourceFile($source->__toString());

        $namespace = $this->getNamespace($ast);
        $useImports = $this->getUseImports($ast);
        var_dump($useImports);die();;
    }

    private function getUseImports(SourceFileNode $node)
    {
        $uses = [];
        foreach ($node->getChildNodes() as $childNode) {
            if (!$childNode instanceof NamespaceUseDeclaration) {
                continue;
            }

            var_dump('wtf');die();;
        }

        return array_combine($uses, $uses);
    }

    private function getNamespace(SourceFileNode $ast)
    {
        $namespace = $ast->getFirstDescendantNode(NamespaceDefinition::class);

        if (null === $namespace) {
            return '';
        }

        return $namespace->name->getText();
    }
}

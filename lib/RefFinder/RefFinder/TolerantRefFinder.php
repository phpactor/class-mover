<?php

namespace DTL\ClassMover\RefFinder\RefFinder;

use Microsoft\PhpParser\Parser;
use DTL\ClassMover\Finder\FileSource;
use Microsoft\PhpParser\Node\Statement\NamespaceDefinition;
use Microsoft\PhpParser\Node\Statement\NamespaceUseDeclaration;
use Microsoft\PhpParser\Node\SourceFileNode;
use DTL\ClassMover\RefFinder\ImportedNamespaceName;
use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Node\QualifiedName;
use Microsoft\PhpParser\Node\NamespaceUseClause;
use DTL\ClassMover\RefFinder\SourceEnvironment;
use DTL\ClassMover\RefFinder\SourceNamespace;
use DTL\ClassMover\RefFinder\QualifiedName as RefQualifiedName;
use DTL\ClassMover\RefFinder\ClassRefList;
use DTL\ClassMover\RefFinder\FullyQualifiedName;

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
        $sourceEnvironment  = $this->getClassEnvironment($namespace, $ast);

        return $this->resolveClassNames($sourceEnvironment, $ast);
    }

    private function resolveClassNames(SourceEnvironment $env, $ast)
    {
        $resolvedClassNames = [];
        $nodes = $ast->getDescendantNodes();

        foreach ($nodes as $node) {
            if (!$node instanceof QualifiedName) {
                continue;
            }

            if ($node->getParent() instanceof NamespaceDefinition) {
                continue;
            }

            if ($node->getParent() instanceof NamespaceUseClause) {
                $resolvedClassNames[] = FullyQualifiedName::fromString($node->getText());
                continue;
            }

            $qualifiedName = RefQualifiedName::fromString($node->getText());

            $resolvedClassName = $env->resolveClassName($qualifiedName);

            if ($env->isAliased($qualifiedName)) {
                continue;
            }
            $resolvedClassNames[] = $resolvedClassName;
        }

        return ClassRefList::fromFullyQualifiedNames($resolvedClassNames);
    }

    private function getClassReferences(SourceFileNode $node)
    {
    }

    private function getClassEnvironment(SourceNamespace $namespace, SourceFileNode $node)
    {
        $uses = [];
        foreach ($node->getChildNodes() as $childNode) {
            if (false === $childNode instanceof NamespaceUseDeclaration) {
                continue;
            }

            $this->populateUseImports($childNode, $uses);
        }

        return SourceEnvironment::fromImportedNames($namespace, $uses);
    }

    private function populateUseImports(NamespaceUseDeclaration $useDeclaration, &$useImports)
    {
        foreach ($useDeclaration->useClauses->getElements() as $useClause) {
            $namespace = ImportedNamespaceName::fromString($useClause->namespaceName->getText());
            $alias = $namespace;

            if ($useClause->namespaceAliasingClause) {
                $alias = $useClause->namespaceAliasingClause->name->getText($useDeclaration->getFileContents());
                $namespace = $namespace->withAlias($alias);
            }

            $useImports[] = $namespace;
        }
    }

    private function getNamespace(SourceFileNode $ast): SourceNamespace
    {
        $namespace = $ast->getFirstDescendantNode(NamespaceDefinition::class);

        if (null === $namespace) {
            return '';
        }

        return SourceNamespace::fromString($namespace->name->getText());
    }
}

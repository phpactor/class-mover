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
use Microsoft\PhpParser\Node\Expression\CallExpression;
use DTL\ClassMover\RefFinder\RefFinder;
use DTL\ClassMover\RefFinder\Position;
use DTL\ClassMover\RefFinder\ClassRef;

class TolerantRefFinder implements RefFinder
{
    private $parser;

    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
    }

    public function findIn(FileSource $source): ClassRefList
    {
        $ast = $this->parser->parseSourceFile($source->__toString());

        $namespace = $this->getNamespace($ast);
        $sourceEnvironment  = $this->getClassEnvironment($namespace, $ast);

        return $this->resolveClassNames($source, $sourceEnvironment, $ast);
    }

    private function resolveClassNames($source, SourceEnvironment $env, $ast)
    {
        $classRefs = [];
        $nodes = $ast->getDescendantNodes();

        foreach ($nodes as $node) {
            // we want QualifiedNames
            if (!$node instanceof QualifiedName) {
                continue;
            }

            // (the) namepspace definition is not interesting
            if ($node->getParent() instanceof NamespaceDefinition) {
                continue;
            }

            if ($node->getParent() instanceof CallExpression) {
                continue;
            }

            // we want to replace all fully qualified use statements
            if ($node->getParent() instanceof NamespaceUseClause) {
                $classRefs[] = ClassRef::fromNameAndPosition(
                    FullyQualifiedName::fromString($node->getText()),
                    Position::fromStartAndEnd($node->getStart(), $node->getEndPosition())
                );
                continue;
            }

            $qualifiedName = RefQualifiedName::fromString($node->getText());
            $resolvedClassName = $env->resolveClassName($qualifiedName);

            // if the name is aliased, then we can safely ignore it
            if ($env->isAliased($qualifiedName)) {
                continue;
            }

            // this is a fully qualified class name
            $classRefs[] = ClassRef::fromNameAndPosition(
                $resolvedClassName,
                Position::fromStartAndEnd($node->getStart(), $node->getEndPosition())
            );
        }

        return ClassRefList::fromClassRefs($source->path(), $classRefs);
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

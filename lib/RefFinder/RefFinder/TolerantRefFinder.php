<?php

namespace DTL\ClassMover\RefFinder\RefFinder;

use Microsoft\PhpParser\Parser;
use DTL\ClassMover\Finder\FileSource;
use Microsoft\PhpParser\Node\Statement\NamespaceDefinition;
use Microsoft\PhpParser\Node\Statement\NamespaceUseDeclaration;
use Microsoft\PhpParser\Node\SourceFileNode;
use DTL\ClassMover\RefFinder\ImportedName;
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
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use DTL\ClassMover\RefFinder\NamespacedClassRefList;
use DTL\ClassMover\RefFinder\NamespaceRef;
use DTL\ClassMover\RefFinder\ImportedNameRef;

class TolerantRefFinder implements RefFinder
{
    private $parser;

    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
    }

    public function findIn(FileSource $source): NamespacedClassRefList
    {
        $ast = $this->parser->parseSourceFile($source->__toString());

        $namespaceRef = $this->getNamespaceRef($ast);
        $sourceEnvironment  = $this->getClassEnvironment($namespaceRef->namespace(), $ast);

        return $this->resolveClassNames($namespaceRef, $source, $sourceEnvironment, $ast);
    }

    private function resolveClassNames(NamespaceRef $namespaceRef, $source, SourceEnvironment $env, $ast)
    {
        $classRefs = [];
        $nodes = $ast->getDescendantNodes();

        foreach ($nodes as $node) {

            if ($node instanceof ClassDeclaration) {
                $namespace = $node->getNamespaceDefinition();
                $name = $node->name->getText($node->getFileContents());
                $classRefs[] = ClassRef::fromNameAndPosition(
                    RefQualifiedName::fromString($name),
                    FullyQualifiedName::fromString(($namespace ? $namespace->name->getText() . '\\' : '') . $name),
                    Position::fromStartAndEnd($node->name->start, $node->name->start + $node->name->length - 1),
                    ImportedNameRef::none(),
                    true
                );
                continue;
            }

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
                    FullyQualifiedName::fromString($node->getText()),
                    Position::fromStartAndEnd($node->getStart(), $node->getEndPosition()),
                    ImportedNameRef::none()
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
                $qualifiedName,
                $resolvedClassName,
                Position::fromStartAndEnd($node->getStart(), $node->getEndPosition()),
                $env->isNameImported($qualifiedName) ? $env->getImportedNameRefFor($qualifiedName) : ImportedNameRef::none()
            );
        }

        return NamespacedClassRefList::fromNamespaceAndClassRefs($namespaceRef, $source->path(), $classRefs);
    }

    private function getClassEnvironment(SourceNamespace $namespace, SourceFileNode $node)
    {
        $useImportRefs = [];
        foreach ($node->getChildNodes() as $childNode) {
            if (false === $childNode instanceof NamespaceUseDeclaration) {
                continue;
            }

            $this->populateUseImportRefs($childNode, $useImportRefs);
        }

        return SourceEnvironment::fromImportedNameRefs($namespace, $useImportRefs);
    }

    private function populateUseImportRefs(NamespaceUseDeclaration $useDeclaration, &$useImportRefs)
    {
        foreach ($useDeclaration->useClauses->getElements() as $useClause) {
            $importedName = ImportedName::fromString($useClause->namespaceName->getText());
            $alias = $importedName;

            if ($useClause->namespaceAliasingClause) {
                $alias = $useClause->namespaceAliasingClause->name->getText($useDeclaration->getFileContents());
                $importedName = $importedName->withAlias($alias);
            }

            $useImportRefs[] = ImportedNameRef::fromImportedNameAndPosition($importedName, Position::fromStartAndEnd(
                $useDeclaration->getStart(),
                $useDeclaration->getEndPosition()
            ));

        }
    }

    private function getNamespaceRef(SourceFileNode $ast): NamespaceRef
    {
        $namespace = $ast->getFirstDescendantNode(NamespaceDefinition::class);

        if (null === $namespace) {
            return NamespaceRef::forRoot();
        }

        return NamespaceRef::fromNameAndPosition(
            SourceNamespace::fromString($namespace->name->getText()),
            Position::fromStartAndEnd(
                $namespace->name->getStart(),
                $namespace->name->getEndPosition()
            )
        );
    }
}

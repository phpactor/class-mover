<?php

namespace Phpactor\ClassMover\Adapter\TolerantParser;

use Microsoft\PhpParser\Node\Expression\CallExpression;
use Microsoft\PhpParser\Node\NamespaceUseClause;
use Microsoft\PhpParser\Node\QualifiedName as ParserQualifiedName;
use Microsoft\PhpParser\Node\SourceFileNode;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;
use Microsoft\PhpParser\Node\Statement\NamespaceDefinition;
use Microsoft\PhpParser\Node\Statement\NamespaceUseDeclaration;
use Microsoft\PhpParser\Node\Statement\TraitDeclaration;
use Microsoft\PhpParser\Parser;
use Phpactor\ClassMover\Domain\ClassReference;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Phpactor\ClassMover\Domain\ImportedName;
use Phpactor\ClassMover\Domain\ImportedNameReference;
use Phpactor\ClassMover\Domain\NamespaceReference;
use Phpactor\ClassMover\Domain\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\Position;
use Phpactor\ClassMover\Domain\QualifiedName;
use Phpactor\ClassMover\Domain\RefFinder;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\SourceImportTable;
use Phpactor\ClassMover\Domain\Namespace_;

class TolerantRefFinder implements RefFinder
{
    private $parser;

    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
    }

    public function findIn(SourceCode $source): NamespacedClassReferences
    {
        $ast = $this->parser->parseSourceFile($source->__toString());

        $namespaceRef = $this->getNamespaceRef($ast);
        $sourceEnvironment = $this->getClassEnvironment($namespaceRef->namespace(), $ast);

        $classRefs = $this->resolveClassNames($source, $sourceEnvironment, $ast);

        return NamespacedClassReferences::fromNamespaceAndClassRefs($namespaceRef, $classRefs);
    }

    private function resolveClassNames($source, SourceImportTable $env, $ast): array
    {
        $classRefs = [];
        $nodes = $ast->getDescendantNodes();

        foreach ($nodes as $node) {
            if (
                $node instanceof ClassDeclaration ||
                $node instanceof InterfaceDeclaration ||
                $node instanceof TraitDeclaration
            ) {
                $namespace = $node->getNamespaceDefinition();

                $name = $node->name->getText($node->getFileContents());
                $classRefs[] = ClassReference::fromNameAndPosition(
                    QualifiedName::fromString($name),
                    FullyQualifiedName::fromString(($namespace && $namespace->name ? $namespace->name->getText().'\\' : '').$name),
                    Position::fromStartAndEnd($node->name->start, $node->name->start + $node->name->length - 1),
                    ImportedNameReference::none(),
                    true
                );
                continue;
            }

            // we want QualifiedNames
            if (!$node instanceof ParserQualifiedName) {
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
                $classRefs[] = ClassReference::fromNameAndPosition(
                    FullyQualifiedName::fromString($node->getText()),
                    FullyQualifiedName::fromString($node->getText()),
                    Position::fromStartAndEnd($node->getStart(), $node->getEndPosition()),
                    ImportedNameReference::none()
                );
                continue;
            }

            $qualifiedName = QualifiedName::fromString($node->getText());
            $resolvedClassName = $env->resolveClassName($qualifiedName);

            // if the name is aliased, then we can safely ignore it
            if ($env->isAliased($qualifiedName)) {
                continue;
            }

            // this is a fully qualified class name
            $classRefs[] = ClassReference::fromNameAndPosition(
                $qualifiedName,
                $resolvedClassName,
                Position::fromStartAndEnd($node->getStart(), $node->getEndPosition()),
                $env->isNameImported($qualifiedName) ? $env->getImportedNameRefFor($qualifiedName) : ImportedNameReference::none()
            );
        }

        return $classRefs;
    }

    private function getClassEnvironment(Namespace_ $namespace, SourceFileNode $node)
    {
        $useImportRefs = [];
        foreach ($node->getChildNodes() as $childNode) {
            if (false === $childNode instanceof NamespaceUseDeclaration) {
                continue;
            }

            $this->populateUseImportRefs($childNode, $useImportRefs);
        }

        return SourceImportTable::fromImportedNameRefs($namespace, $useImportRefs);
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

            $useImportRefs[] = ImportedNameReference::fromImportedNameAndPosition($importedName, Position::fromStartAndEnd(
                $useDeclaration->getStart(),
                $useDeclaration->getEndPosition()
            ));
        }
    }

    private function getNamespaceRef(SourceFileNode $ast): NamespaceReference
    {
        $namespace = $ast->getFirstDescendantNode(NamespaceDefinition::class);

        if (null === $namespace) {
            return NamespaceReference::forRoot();
        }

        if (null === $namespace->name) {
            return NamespaceReference::forRoot();
        }

        return NamespaceReference::fromNameAndPosition(
            Namespace_::fromString($namespace->name->getText()),
            Position::fromStartAndEnd(
                $namespace->name->getStart(),
                $namespace->name->getEndPosition()
            )
        );
    }
}

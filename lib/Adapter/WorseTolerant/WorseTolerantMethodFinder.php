<?php

namespace Phpactor\ClassMover\Adapter\WorseTolerant;

use Phpactor\ClassMover\Domain\MethodFinder;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\WorseReflection\Reflector;
use Phpactor\WorseReflection\Core\SourceCodeLocator\StringSourceLocator;
use Phpactor\WorseReflection\Core\SourceCode as WorseSourceCode;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Node\Expression\CallExpression;
use Phpactor\ClassMover\Domain\Reference\MethodReference;
use Phpactor\ClassMover\Domain\Reference\Position;
use Phpactor\WorseReflection\Core\Offset;
use Phpactor\ClassMover\Domain\Model\Class_;
use Microsoft\PhpParser\Node\Expression\MemberAccessExpression;
use Microsoft\PhpParser\Node\Expression\ScopedPropertyAccessExpression;
use Phpactor\WorseReflection\Core\ClassName;
use Phpactor\ClassMover\Domain\Name\MethodName;
use Phpactor\WorseReflection\Core\Exception\NotFound;
use Microsoft\PhpParser\Token;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\Statement\TraitDeclaration;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;

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
        $this->reflector = $reflector ?: Reflector::create(new StringSourceLocator(WorseSourceCode::fromString('')));
        $this->parser = $parser ?: new Parser();
    }

    public function findMethods(SourceCode $source, ClassMethodQuery $query): MethodReferences
    {
        $rootNode = $this->parser->parseSourceFile((string) $source);
        $expressions = $this->collectCallExpressions($rootNode, $query);

        if ($query->hasClass()) {
            $expressions = $this->filterByClass($query->class(), $expressions);
        }

        $references = [];
        foreach ($expressions as $expression) {
            if ($expression instanceof MethodDeclaration) {

                if (false === $expression->name instanceof Token) {
                    // todo: Log this
                    continue;
                }

                $references[] = MethodReference::fromMethodNameAndPosition(
                    MethodName::fromString((string) $expression->name->getText($expression->getFileContents())),
                    Position::fromStartAndEnd(
                        $expression->name->start,
                        $expression->name->start + $expression->name->length
                    )
                );
                continue;
            }

            if (false === $expression->memberName instanceof Token) {
                // todo: Log this
                continue;
            }

            $references[] = MethodReference::fromMethodNameAndPosition(
                MethodName::fromString((string) $expression->memberName->getText($expression->getFileContents())),
                Position::fromStartAndEnd(
                    $expression->memberName->start,
                    $expression->memberName->start + $expression->memberName->length
                )
            );
        }

        return MethodReferences::fromMethodReferences($references);
    }

    private function filterByClass(Class_ $class, array $expressions)
    {
        return array_filter($expressions, function (Node $expression) use ($class) {
            if ($expression instanceof MemberAccessExpression) {
                return $this->isMemberAccessExpressionMemberOfClass($class, $expression);
            }

            if ($expression instanceof ScopedPropertyAccessExpression) {
                return $this->isScopedPropertyAccessExpressionMemberOfClass($class, $expression);
            }

            if ($expression instanceof MethodDeclaration) {
                // already matched
                return true;
            }
        });
    }

    private function isScopedPropertyAccessExpressionMemberOfClass(Class_ $class, ScopedPropertyAccessExpression $expression)
    {
        if ((string) $expression->scopeResolutionQualifier->getResolvedName() == (string) $class) {
            return true;
        }

        return false;
    }

    private function isMemberAccessExpressionMemberOfClass(Class_ $class, MemberAccessExpression $expression)
    {
        $offset = $this->reflector->reflectOffset(
            WorseSourceCode::fromString($expression->getFileContents()),
            Offset::fromInt($expression->dereferencableExpression->getEndPosition())
        );

        $type = $offset->symbolInformation()->type();
        if (false === $type->isClass() || false === $type->isDefined()) {
            return false;
        }

        try {
            $reflectionClass = $this->reflector->reflectClass($type->className());
        } catch (NotFound $notFound) {
            return false;
        }

        if ($reflectionClass->isInstanceOf(ClassName::fromString((string) $class))) {
            return true;
        }

        return false;
    }

    private function collectCallExpressions(Node $node, ClassMethodQuery $query): array
    {
        $expressions = [];
        $methodName = null;

        if ($node instanceof MethodDeclaration) {
            if ($this->isMatchingMethodDeclaration($node, $query)) {
                $expressions[] = $node;
            }
        }

        if ($this->isMethodCall($node)) {
            $methodName = $node->callableExpression->memberName->getText($node->getFileContents());

            if ($query->matchesMethodName($methodName)) {
                $expressions[] = $node->callableExpression;
            }
        }

        foreach ($node->getChildNodes() as $childNode) {
            $expressions = array_merge($expressions, $this->collectCallExpressions($childNode, $query));
        }

        return $expressions;
    }

    private function isMatchingMethodDeclaration(MethodDeclaration $node, ClassMethodQuery $query)
    {
        if (false === $query->matchesMethodName($node->name->getText($node->getFileContents()))) {
            return false;

        }

        $classNode = $node->getFirstAncestor(ClassDeclaration::class, InterfaceDeclaration::class, TraitDeclaration::class);

        if (null === $classNode) {
            return false;
        }

        $className = ClassName::fromString($classNode->getNamespacedName());

        try {
            $reflectionClass = $this->reflector->reflectClass($className);
        } catch (NotFound $notFound) {
            return false;
        }

        if (false === $reflectionClass->isInstanceOf(ClassName::fromString((string) $query->class()))) {
            return false;
        }

        return true;
    }
    
    private function isMethodCall(Node $node)
    {
        if (false === $node instanceof CallExpression) {
            return false;
        }

        if (null === $node->callableExpression) {
            return false;
        }

        return 
            $node->callableExpression instanceof MemberAccessExpression || 
            $node->callableExpression instanceof ScopedPropertyAccessExpression;
    }
}


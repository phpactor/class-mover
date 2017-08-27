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
use Phpactor\WorseReflection\Core\Type;

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
        $methodNodes = $this->collectCallNodes($rootNode, $query);

        $references = [];
        foreach ($methodNodes as $methodNode) {
            if ($methodNode instanceof ScopedPropertyAccessExpression && $reference = $this->getScopedPropertyAccessReference($query, $methodNode)) {
                $references[] = $reference;
                continue;
            }

            if ($methodNode instanceof MemberAccessExpression && $reference = $this->getMemberAccessReference($query, $methodNode)) {
                $references[] = $reference;
                continue;
            }

            if ($methodNode instanceof MethodDeclaration && $reference = $this->getMethodDeclarationReference($query, $methodNode)) {
                $references[] = $reference;
                continue;
            }
        }

        return MethodReferences::fromMethodReferences($references);
    }

    /**
     * Collect all nodes which reference the method NAME.
     * We will check if they belong to the requested class later.
     */
    private function collectCallNodes(Node $node, ClassMethodQuery $query): array
    {
        $methodNodes = [];
        $methodName = null;

        if ($node instanceof MethodDeclaration) {
            $methodName = $node->name->getText($node->getFileContents());

            if ($query->matchesMethodName($methodName)) {
                $methodNodes[] = $node;
            }
        }

        if ($this->isMethodCall($node)) {
            $methodName = $node->callableExpression->memberName->getText($node->getFileContents());

            if ($query->matchesMethodName($methodName)) {
                $methodNodes[] = $node->callableExpression;
            }
        }

        foreach ($node->getChildNodes() as $childNode) {
            $methodNodes = array_merge($methodNodes, $this->collectCallNodes($childNode, $query));
        }

        return $methodNodes;
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

    private function getMethodDeclarationReference(ClassMethodQuery $query, MethodDeclaration $methodNode)
    {
        // we don't handle Variable calls yet.
        if (false === $methodNode->name instanceof Token) {
            return;
        }

        $reference = MethodReference::fromMethodNameAndPosition(
            MethodName::fromString((string) $methodNode->name->getText($methodNode->getFileContents())),
            Position::fromStartAndEnd(
                $methodNode->name->start,
                $methodNode->name->start + $methodNode->name->length - 1
            )
        );

        $classNode = $methodNode->getFirstAncestor(ClassDeclaration::class, InterfaceDeclaration::class, TraitDeclaration::class);

        // if no class node found, then this is not valid
        // TODO: Log this.
        if (null === $classNode) {
            return;
        }

        $className = ClassName::fromString($classNode->getNamespacedName());
        $reference = $reference->withClass(Class_::fromString($className));

        try {
            $reflectionClass = $this->reflector->reflectClass($className);
        } catch (NotFound $notFound) {
            return;
        }

        // if the references class is not an instance of the requested class, then
        // ignore it.
        if (false === $reflectionClass->isInstanceOf(ClassName::fromString((string) $query->class()))) {
            return;
        }

        return $reference;
    }

    /**
     * Get static method call.
     * TODO: This does not support overridden static methods.
     */
    private function getScopedPropertyAccessReference(ClassMethodQuery $query, ScopedPropertyAccessExpression $methodNode)
    {
        $className = $methodNode->scopeResolutionQualifier->getResolvedName();

        if ($className != (string) $query->class()) {
            return;
        }

        return MethodReference::fromMethodNameAndPositionAndClass(
            MethodName::fromString((string) $methodNode->memberName->getText($methodNode->getFileContents())),
            Position::fromStartAndEnd(
                $methodNode->memberName->start,
                $methodNode->memberName->start + $methodNode->memberName->length
            ),
            Class_::fromString($className)
        );
    }

    private function getMemberAccessReference(ClassMethodQuery $query, MemberAccessExpression $methodNode)
    {
        if (false === $methodNode->memberName instanceof Token) {
            return;
        }

        $reference = MethodReference::fromMethodNameAndPosition(
            MethodName::fromString((string) $methodNode->memberName->getText($methodNode->getFileContents())),
            Position::fromStartAndEnd(
                $methodNode->memberName->start,
                $methodNode->memberName->start + $methodNode->memberName->length
            )
        );

        $offset = $this->reflector->reflectOffset(
            WorseSourceCode::fromString($methodNode->getFileContents()),
            Offset::fromInt($methodNode->dereferencableExpression->getEndPosition())
        );

        $type = $offset->symbolInformation()->type();

        if ($query->hasMethod() && Type::unknown() == $type) {
            return $reference;
        }

        if (false === $type->isClass()) {
            return false;
        }

        try {
            $reflectionClass = $this->reflector->reflectClass($type->className());

            if ($query->hasClass() && false === $reflectionClass->isInstanceOf(ClassName::fromString((string) $query->class()))) {
                return;
            }
        } catch (NotFound $notFound) {
            return $reference;
        }

        return $reference->withClass(Class_::fromString((string) $type->className()));
    }
}

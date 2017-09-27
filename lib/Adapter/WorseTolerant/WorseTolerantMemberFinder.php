<?php

namespace Phpactor\ClassMover\Adapter\WorseTolerant;

use Phpactor\ClassMover\Domain\MemberFinder;
use Phpactor\ClassMover\Domain\Reference\MemberReferences;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Model\ClassMemberQuery;
use Phpactor\WorseReflection\Reflector;
use Phpactor\WorseReflection\Core\SourceCodeLocator\StringSourceLocator;
use Phpactor\WorseReflection\Core\SourceCode as WorseSourceCode;
use Microsoft\PhpParser\Parser;
use Microsoft\PhpParser\Node;
use Microsoft\PhpParser\Node\Expression\CallExpression;
use Phpactor\ClassMover\Domain\Reference\MemberReference;
use Phpactor\ClassMover\Domain\Reference\Position;
use Phpactor\WorseReflection\Core\Offset;
use Phpactor\ClassMover\Domain\Model\Class_;
use Microsoft\PhpParser\Node\Expression\MemberAccessExpression;
use Microsoft\PhpParser\Node\Expression\ScopedPropertyAccessExpression;
use Phpactor\WorseReflection\Core\ClassName;
use Phpactor\ClassMover\Domain\Name\MemberName;
use Phpactor\WorseReflection\Core\Exception\NotFound;
use Microsoft\PhpParser\Token;
use Microsoft\PhpParser\Node\MethodDeclaration;
use Microsoft\PhpParser\Node\Statement\ClassDeclaration;
use Microsoft\PhpParser\Node\Statement\TraitDeclaration;
use Microsoft\PhpParser\Node\Statement\InterfaceDeclaration;
use Phpactor\WorseReflection\Core\Type;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Phpactor\WorseReflection\Core\Reflection\AbstractReflectionClass;
use Phpactor\WorseReflection\Core\Reflection\ReflectionClass;

class WorseTolerantMemberFinder implements MemberFinder
{
    /**
     * @var Reflector
     */
    private $reflector;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Reflector $reflector = null, Parser $parser = null, LoggerInterface $logger = null)
    {
        $this->reflector = $reflector ?: Reflector::create(new StringSourceLocator(WorseSourceCode::fromString('')));
        $this->parser = $parser ?: new Parser();
        $this->logger = $logger ?: new NullLogger();
    }

    public function findMembers(SourceCode $source, ClassMemberQuery $query): MemberReferences
    {
        $rootNode = $this->parser->parseSourceFile((string) $source);
        $methodNodes = $this->collectMemberReferences($rootNode, $query);

        $queryClassReflection = null;
        // TODO: Factor this to a method
        if ($query->hasClass()) {
            $queryClassReflection = $this->resolveBaseReflectionClass($query);
        }

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

            if ($methodNode instanceof MethodDeclaration && $reference = $this->getMethodDeclarationReference($queryClassReflection, $methodNode)) {

                $references[] = $reference;
                continue;
            }
        }

        return MemberReferences::fromMemberReferences($references);
    }

    /**
     * Collect all nodes which reference the method NAME.
     * We will check if they belong to the requested class later.
     */
    private function collectMemberReferences(Node $node, ClassMemberQuery $query): array
    {
        $methodNodes = [];
        $memberName = null;

        if ($node instanceof MethodDeclaration) {
            $memberName = $node->name->getText($node->getFileContents());

            if ($query->matchesMemberName($memberName)) {
                $methodNodes[] = $node;
            }
        }

        if ($this->isMemberAccess($node)) {
            $memberName = $node->callableExpression->memberName->getText($node->getFileContents());

            if ($query->matchesMemberName($memberName)) {
                $methodNodes[] = $node->callableExpression;
            }
        }

        foreach ($node->getChildNodes() as $childNode) {
            $methodNodes = array_merge($methodNodes, $this->collectMemberReferences($childNode, $query));
        }

        return $methodNodes;
    }

    private function isMemberAccess(Node $node)
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

    private function getMethodDeclarationReference(AbstractReflectionClass $queryClass = null, MethodDeclaration $methodNode)
    {
        // we don't handle Variable calls yet.
        if (false === $methodNode->name instanceof Token) {
            $this->logger->warning('Do not know how to infer method name from variable');
            return;
        }

        $reference = MemberReference::fromMemberNameAndPosition(
            MemberName::fromString((string) $methodNode->name->getText($methodNode->getFileContents())),
            Position::fromStartAndEnd(
                $methodNode->name->start,
                $methodNode->name->start + $methodNode->name->length - 1
            )
        );

        $classNode = $methodNode->getFirstAncestor(ClassDeclaration::class, InterfaceDeclaration::class, TraitDeclaration::class);

        // if no class node found, then this is not valid, don't know how to reproduce this, probably
        // not a possible scenario with the parser.
        if (null === $classNode) {
            return;
        }

        $className = ClassName::fromString($classNode->getNamespacedName());
        $reference = $reference->withClass(Class_::fromString($className));

        if (null === $queryClass) {
            return $reference;
        }

        if (null === $reflectionClass = $this->reflectClass($className)) {
            $this->logger->warning(sprintf('Could not find class "%s" for method declaration, ignoring it', (string) $className));
            return;
        }

        // if the references class is not an instance of the requested class, or the requested class is not
        // an instance of the referenced class then ignore it.
        if (false === $reflectionClass->isTrait() && false === $reflectionClass->isInstanceOf($queryClass->name())) {
            return;
        }

        return $reference;
    }

    /**
     * Get static method call.
     * TODO: This does not support overridden static methods.
     */
    private function getScopedPropertyAccessReference(ClassMemberQuery $query, ScopedPropertyAccessExpression $methodNode)
    {
        $className = $methodNode->scopeResolutionQualifier->getResolvedName();

        if ($query->hasClass() && $className != (string) $query->class()) {
            return;
        }

        return MemberReference::fromMemberNamePositionAndClass(
            MemberName::fromString((string) $methodNode->memberName->getText($methodNode->getFileContents())),
            Position::fromStartAndEnd(
                $methodNode->memberName->start,
                $methodNode->memberName->start + $methodNode->memberName->length
            ),
            Class_::fromString($className)
        );
    }

    private function getMemberAccessReference(ClassMemberQuery $query, MemberAccessExpression $methodNode)
    {
        if (false === $methodNode->memberName instanceof Token) {
            $this->logger->warning('Do not know how to infer method name from variable');
            return;
        }

        $reference = MemberReference::fromMemberNameAndPosition(
            MemberName::fromString((string) $methodNode->memberName->getText($methodNode->getFileContents())),
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

        if ($query->hasMember() && Type::unknown() == $type) {
            return $reference;
        }

        if (false === $type->isClass()) {
            return;
        }


        if (false === $query->hasClass()) {
            $reference = $reference->withClass(Class_::fromString((string) $type->className()));
            return $reference;
        }

        if (null === $reflectionClass = $this->reflectClass($type->className())) {
            $this->logger->warning(sprintf('Could not find class "%s", logging as risky', (string) $type->className()));
            return $reference;
        }
        if (false === $reflectionClass->isInstanceOf(ClassName::fromString((string) $query->class()))) {
            // is not the correct class
            return;
        }

        return $reference->withClass(Class_::fromString((string) $type->className()));
    }

    /**
     * @return ReflectionClass
     */
    private function reflectClass(ClassName $className)
    {
        try {
            return $this->reflector->reflectClassLike($className);
        } catch (NotFound $e) {
            return null;
        }
    }

    /**
     * @return ReflectionClass
     */
    private function resolveBaseReflectionClass(ClassMemberQuery $query)
    {
        $queryClassReflection = $this->reflectClass(ClassName::fromString((string) $query->class()));
        if (null === $queryClassReflection) {
            return $queryClassReflection;
        }

        $methods = $queryClassReflection->methods();

        if (false === $query->hasMember()) {
            return $queryClassReflection;
        }

        if (false === $methods->has($query->memberName())) {
            return $queryClassReflection;
        }

        if (false === $queryClassReflection->isClass()) {
            return $queryClassReflection;
        }

        // TODO: Support the case where interfaces both implement the same method
        foreach ($queryClassReflection->interfaces() as $interfaceReflection) {
            if ($interfaceReflection->methods()->has($query->memberName())) {
                $queryClassReflection = $interfaceReflection;
                break;
            }
        }

        return $queryClassReflection;
    }
}

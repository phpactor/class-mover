<?php

namespace Phpactor\ClassMover\Domain\Model;

use Phpactor\ClassMover\Domain\Name\MethodName;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;

final class ClassMethodQuery
{
    /**
     * @var Class_
     */
    private $class;

    /**
     * @var MethodName
     */
    private $methodName;

    private function __construct(Class_ $class = null, MethodName $methodName = null)
    {
        $this->class = $class;
        $this->methodName = $methodName;
    }

    public static function fromClassAndMethodName(Class_ $class, MethodName $methodName): ClassMethodQuery
    {
         return new self($class, $methodName);
    }

    public static function fromScalarClassAndMethodName(string $className, string $methodName): ClassMethodQuery
    {
        return new self(Class_::fromFullyQualifiedName(FullyQualifiedName::fromString($className)), MethodName::fromString($methodName));
    }

    public static function fromScalarClass(string $className): ClassMethodQuery
    {
        return new self(Class_::fromFullyQualifiedName(FullyQualifiedName::fromString($className)));
    }

    public static function all(): ClassMethodQuery
    {
        return new self();
    }

    public function methodName(): MethodName
    {
        return $this->methodName;
    }

    public function matchesMethodName(string $methodName)
    {
        if (null === $this->methodName) {
            return true;
        }

        return $methodName == (string) $this->methodName;
    }

    public function matchesClass(string $className)
    {
        if (null === $this->class) {
            return true;
        }

        return $className == (string) $this->class;
    }

    public function class(): Class_
    {
        return $this->class;
    }

    public function hasClass(): bool
    {
        return null !== $this->class;
    }

    public function __toString()
    {
        return $this->class;
    }
}

<?php

namespace Phpactor\ClassMover\Domain\Model;

use Phpactor\ClassMover\Domain\Name\MethodName;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;

final class ClassMethod
{
    /**
     * @var Class_
     */
    private $class;

    /**
     * @var MethodName
     */
    private $methodName;

    private function __construct(Class_ $class, MethodName $methodName)
    {
        $this->class = $class;
        $this->methodName = $methodName;
    }

    public static function fromClassAndMethodName(Class_ $class, MethodName $methodName): ClassMethod
    {
         return new self($class, $methodName);
    }

    public static function fromScalarClassAndMethodName(string $className, string $methodName): ClassMethod
    {
        return new self(Class_::fromFullyQualifiedName(FullyQualifiedName::fromString($className)), MethodName::fromString($methodName));
    }

    public function methodName(): MethodName
    {
        return $this->methodName;
    }

    public function class(): Class_
    {
        return $this->class;
    }

    public function __toString()
    {
        return $this->class;
    }
}


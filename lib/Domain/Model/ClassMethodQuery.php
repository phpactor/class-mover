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

    public static function create(): ClassMethodQuery
    {
        return new self();
    }

    /**
     * @var Class_|string
     */
    public function withClass($className): ClassMethodQuery
    {
        if (false === is_string($className) && false === $className instanceof Class_) {
            throw new \InvalidArgumentException(sprintf(
                'Class must be either a string or an instanceof Class_, got: "%s"',
                gettype($className)
            ));
        }

        return new self(
            is_string($className) ? Class_::fromString($className) : $className,
            $this->methodName
        );
    }

    /**
     * @var MethodName|string
     */
    public function withMethod($methodName): ClassMethodQuery
    {
        if (false === is_string($methodName) && false === $methodName instanceof MethodName) {
            throw new \InvalidArgumentException(sprintf(
                'Method must be either a string or an instanceof MethodName, got: "%s"',
                gettype($methodName)
            ));
        }

        return new self(
            $this->class,
            is_string($methodName) ? MethodName::fromString($methodName) : $methodName
        );
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

    public function hasMethod(): bool
    {
        return null !== $this->methodName;
    }

    public function __toString()
    {
        return $this->class;
    }
}

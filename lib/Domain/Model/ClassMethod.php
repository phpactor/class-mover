<?php

namespace Phpactor\ClassMover\Domain\Model;

use Phpactor\ClassMover\Domain\Name\MethodName;

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

    public function __toString()
    {
        return $this->class;
    }
}


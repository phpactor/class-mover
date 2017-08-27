<?php

namespace Phpactor\ClassMover\Domain\Reference;

use Phpactor\ClassMover\Domain\Reference\Position;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\ClassMover\Domain\Name\MethodName;
use Phpactor\ClassMover\Domain\Model\Class_;

class MethodReference
{
    /**
     * @var MethodName
     */
    private $method;

    /**
     * @var Position
     */
    private $position;

    /**
     * @var Class_
     */
    private $class;

    private function __construct(MethodName $method, Position $position, Class_ $class = null)
    {
        $this->method = $method;
        $this->position = $position;
        $this->class = $class;
    }

    public static function fromMethodNameAndPosition(MethodName $method, Position $position): MethodReference
    {
        return new self($method, $position);
    }

    public static function fromMethodNameAndPositionAndClass(MethodName $method, Position $position, Class_ $class): MethodReference
    {
        return new self($method, $position, $class);
    }

    public function methodName(): MethodName
    {
        return $this->method;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function hasClass(): bool
    {
        return null !== $this->class;
    }

    public function withClass(Class_ $class)
    {
        return new self($this->method, $this->position, $class);
    }

    public function class(): Class_
    {
        return $this->class;
    }

    public function __toString()
    {
        return sprintf('[%s:%s] %s', $this->position->start(), $this->position->end(), (string) $this->methodName);
    }
}


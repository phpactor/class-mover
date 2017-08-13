<?php

namespace Phpactor\ClassMover\Domain\Reference;

use Phpactor\ClassMover\Domain\Reference\Position;
use Phpactor\ClassMover\Domain\Model\ClassMethod;

class MethodReference
{
    /**
     * @var ClassMethod
     */
    private $method;

    /**
     * @var Position
     */
    private $position;

    private function __construct(ClassMethod $method, Position $position)
    {
        $this->method = $method;
        $this->position = $position;
    }

    public function fromMethodAndPosition(ClassMethod $method, Position $position)
    {
        return new self($method, $position);
    }
}


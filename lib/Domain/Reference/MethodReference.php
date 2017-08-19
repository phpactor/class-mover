<?php

namespace Phpactor\ClassMover\Domain\Reference;

use Phpactor\ClassMover\Domain\Reference\Position;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;

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

    private function __construct(ClassMethodQuery $method, Position $position)
    {
        $this->method = $method;
        $this->position = $position;
    }

    public function fromMethodAndPosition(ClassMethodQuery $method, Position $position)
    {
        return new self($method, $position);
    }

    public function method(): ClassMethodQuery
    {
        return $this->method;
    }

    public function position(): Position
    {
        return $this->position;
    }
}


<?php

namespace Phpactor\ClassMover\Domain\Reference;

use Phpactor\ClassMover\Domain\Reference\Position;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\ClassMover\Domain\Name\MethodName;

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

    private function __construct(MethodName $method, Position $position)
    {
        $this->method = $method;
        $this->position = $position;
    }

    public static function fromMethodNameAndPosition(MethodName $method, Position $position)
    {
        return new self($method, $position);
    }

    public function methodName(): MethodName
    {
        return $this->method;
    }

    public function position(): Position
    {
        return $this->position;
    }
}


<?php

namespace Phpactor\ClassMover\Domain\Model;

class ReferenceType
{
    /**
     * @var string
     */
    private $referenceType;

    private function __construct(string $referenceType)
    {
        $this->referenceType = $referenceType;
    }

    public static function CLASS(): self
    {
        return new self('class');
    }

    public static function INTERFACE(): self
    {
        return new self('interface');
    }

    public static function TRAIT(): self
    {
        return new self('trait');
    }

    public static function CLASS_IMPORT(): self
    {
        return new self('class_import');
    }

    public static function QUALIFIED_NAME(): self
    {
        return new self('qualified_name');
    }
}

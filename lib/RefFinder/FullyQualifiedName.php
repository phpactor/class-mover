<?php

namespace DTL\ClassMover\RefFinder;

class FullyQualifiedName extends QualifiedName
{
    public static function fromString(string $string)
    {
        return parent::fromString(trim($string, '\\'));
    }
}

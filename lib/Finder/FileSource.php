<?php

namespace DTL\ClassMover\Finder;

final class FileSource
{
    private $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public static function fromString(string $source)
    {
        return new self($source);
    }

    public function __toString()
    {
        return $this->source;
    }
}

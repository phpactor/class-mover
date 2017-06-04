<?php

namespace DTL\ClassMover\Finder;

final class SearchPath
{
    private $path;

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function fromString($path)
    {
        return new self($path);
    }

    public function __toString()
    {
        return $this->path;
    }
}

<?php

namespace DTL\ClassMover\Finder;

use DTL\ClassMover\Finder\FilePath;

final class FileSource
{
    private $source;
    private $path;

    public function __construct(FilePath $path, string $source)
    {
        $this->source = $source;
        $this->path = $path;
    }

    public static function fromFilePathAndString(FilePath $path, string $source)
    {
        return new self($path, $source);
    }

    public function path(): FilePath
    {
        return $this->path;
    }

    public function replaceSource(string $source)
    {
        return new self($this->path, $source);
    }

    public function writeBackToFile()
    {
        file_put_contents($this->path->__toString(), $this->source);
    }

    public function __toString()
    {
        return $this->source;
    }
}

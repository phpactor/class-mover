<?php

namespace DTL\ClassMover\Finder;

final class FilePath
{
    private $path;

    public static function fromString(string $path)
    {
        $new = new self();
        $real = realpath($path);

        if (!$real) {
            throw new \RuntimeException(sprintf(
                'Could not determine realpath for "%s"', $path
            ));
        }

        $new->path = $real;

        return $new;
    }

    public function getSource(): FileSource
    {
        return new FileSource($this, file_get_contents($this->path));
    }

    public function __toString()
    {
        return $this->path;
    }
}

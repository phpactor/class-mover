<?php

namespace DTL\ClassMover\Finder;

final class FilePath
{
    const PATH_NONE = '_|__<transient>__|_';

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

    public static function none()
    {
        $new = new self();
        $new->path = self::PATH_NONE;

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

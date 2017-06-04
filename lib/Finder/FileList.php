<?php

namespace DTL\ClassMover\Finder;

final class FileList implements \IteratorAggregate
{
    private $filePaths = [];

    public function fromFilePaths(array $filePaths)
    {
        $new = new self();
        foreach ($filePaths as $filePath) {
            $new->add($filePath);
        }

        return $new;
    }

    public static function fromStrings(array $strings)
    {
        $new = new self();
        foreach ($strings as $string) {
            $new->add(FilePath::fromString($string));
        }

        return $new;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->filePaths);
    }

    private function add(FilePath $filePath)
    {
        $this->filePaths[] = $filePath;
    }
}

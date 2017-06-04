<?php

namespace DTL\ClassMover\Finder\Finder;

use DTL\ClassMover\Finder\SearchPath;
use DTL\ClassMover\Finder\FileList;

class RecursiveDirectoryFinder
{
    public function findIn(SearchPath $path): FileList
    {
        $directoryIterator = new \RecursiveDirectoryIterator($path->__toString());
        $iteratorIterator = new \RecursiveIteratorIterator($directoryIterator);
        $files = new \RegexIterator($iteratorIterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        $files = array_map(function ($file) {
            return $file[0];
        }, iterator_to_array($files));

        $list = FileList::fromStrings($files);

        return $list;
    }
}

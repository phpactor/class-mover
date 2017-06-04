<?php

namespace DTL\ClassMover\Finder;

interface Finder
{
    public function findIn(SearchPath $path): FileList;
}

<?php

namespace Acme;

use Acme\ClassMover\RefFinder\RefFinder\TolerantRefFinder;

class Hello
{
    public function something()
    {
        TolerantRefFinder::foobar();
    }
}
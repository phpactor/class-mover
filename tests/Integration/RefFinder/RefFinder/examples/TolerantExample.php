<?php

namespace Acme;

use Acme\Foobar\Foobar;
use Acme\Foobar\Barfoo;
use Acme\Barfoo as ZedZed;

class Hello
{
    public function something()
    {
        $foo = new Foobar();
        $bar = new Barfoo\FooFoo();
        ZedZed::something();
    }
}

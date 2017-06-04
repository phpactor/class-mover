<?php

namespace DTL\ClassMover\Tests\Integration\RefFinder\RefFinder;

use Microsoft\PhpParser\Parser;
use DTL\ClassMover\RefFinder\RefFinder\TolerantRefFinder;
use PHPUnit\Framework\TestCase;
use DTL\ClassMover\Finder\FileSource;

class TolerantRefFinderTest extends TestCase
{
    /**
     * @testdox It finds all class references.
     */
    public function testFind()
    {
        $parser = new Parser();
        $tolerantRefFinder = new TolerantRefFinder($parser);
        $source = FileSource::fromString(file_get_contents(__DIR__ . '/examples/TolerantExample.php'));
        $references = $tolerantRefFinder->findIn($source);
        var_dump($references);die();;
    }


}

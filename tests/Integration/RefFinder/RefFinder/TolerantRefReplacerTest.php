<?php

namespace DTL\ClassMover\Tests\Integration\RefFinder\RefFinder;

use Microsoft\PhpParser\Parser;
use DTL\ClassMover\RefFinder\RefFinder\TolerantRefFinder;
use PHPUnit\Framework\TestCase;
use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\Finder\FilePath;
use DTL\ClassMover\RefFinder\RefFinder\TolerantRefReplacer;
use DTL\ClassMover\RefFinder\FullyQualifiedName;

class TolerantRefRepalcerTest extends TestCase
{
    /**
     * @testdox It finds all class references.
     */
    public function testFind()
    {
        $parser = new Parser();
        $tolerantRefFinder = new TolerantRefFinder($parser);
        $source = FileSource::fromFilePathAndString(FilePath::none(), file_get_contents(__DIR__ . '/examples/TolerantExample.php'));
        $names = $tolerantRefFinder->findIn($source)->filterForName(FullyQualifiedName::fromString('Acme\\Foobar\\Warble'));
        $replacer = new TolerantRefReplacer();
        $source = $replacer->replaceReferences($source, $names, FullyQualifiedName::fromString('BarBar\\Hello'));
        $this->assertEquals(<<<'EOT'
<?php

namespace Acme;

use BarBar\Hello;
use Acme\Foobar\Barfoo;
use Acme\Barfoo as ZedZed;

class Hello
{
    public function something()
    {
        $foo = new Hello();
        $bar = new Demo();

        //this should not be found as it is de-referenced (we wil replace the use statement instead)
        ZedZed::something();

        assert(Barfoo::class === 'Foo');
        Barfoo::foobar();
    }
}

EOT
        , $source->__toString());
    }
}


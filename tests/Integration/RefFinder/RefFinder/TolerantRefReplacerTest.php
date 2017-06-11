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
     * @dataProvider provideTestFind
     */
    public function testFind($classFqn, $replaceWithFqn, $expectedSource)
    {
        $parser = new Parser();
        $tolerantRefFinder = new TolerantRefFinder($parser);
        $source = FileSource::fromFilePathAndString(FilePath::none(), file_get_contents(__DIR__ . '/examples/TolerantExample.php'));
        $names = $tolerantRefFinder->findIn($source)->filterForName(FullyQualifiedName::fromString($classFqn));
        $replacer = new TolerantRefReplacer();
        $source = $replacer->replaceReferences($source, $names, FullyQualifiedName::fromString($replaceWithFqn));
        $this->assertContains($expectedSource, $source->__toString());
    }

    public function provideTestFind()
    {
        return [
            [
                'Acme\\Foobar\\Warble',
                'BarBar\\Hello',
                <<<'EOT'
use BarBar\Hello;
use Acme\Foobar\Barfoo;
use Acme\Barfoo as ZedZed;

class Hello
{
    public function something()
    {
        $foo = new Hello();
EOT
            ],
            [
                'Acme\\Hello',
                'Acme\\Definee',
                <<<'EOT'
<?php

namespace Acme;

use Acme\Foobar\Warble;
use Acme\Foobar\Barfoo;
use Acme\Barfoo as ZedZed;

class Definee
EOT
            ],
        ];
    }
}


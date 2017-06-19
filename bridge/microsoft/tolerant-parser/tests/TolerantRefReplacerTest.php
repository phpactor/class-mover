<?php

namespace DTL\ClassMover\Tests\Microsoft\TolerantParser;

use Microsoft\PhpParser\Parser;
use DTL\ClassMover\Bridge\Microsoft\TolerantParser\TolerantRefFinder;
use PHPUnit\Framework\TestCase;
use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\Finder\FilePath;
use DTL\ClassMover\Bridge\Microsoft\TolerantParser\TolerantRefReplacer;
use DTL\ClassMover\RefFinder\FullyQualifiedName;

class TolerantRefRepalcerTest extends TestCase
{
    /**
     * @testdox It finds all class references.
     * @dataProvider provideTestFind
     */
    public function testFind($fileName, $classFqn, $replaceWithFqn, $expectedSource)
    {
        $parser = new Parser();
        $tolerantRefFinder = new TolerantRefFinder($parser);
        $source = FileSource::fromFilePathAndString(FilePath::none(), file_get_contents(__DIR__ . '/examples/' . $fileName));
        $originalName = FullyQualifiedName::fromString($classFqn);

        $names = $tolerantRefFinder->findIn($source)->filterForName($originalName);
        $replacer = new TolerantRefReplacer();
        $source = $replacer->replaceReferences($source, $names, $originalName, FullyQualifiedName::fromString($replaceWithFqn));
        $this->assertContains($expectedSource, $source->__toString());
    }

    public function provideTestFind()
    {
        return [
            'Change references of moved class' => [
                'Example1.php',
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
            'Changes class name of moved class' => [
                'Example1.php',
                'Acme\\Hello',
                'Acme\\Definee',
                <<<'EOT'
namespace Acme;

use Acme\Foobar\Warble;
use Acme\Foobar\Barfoo;
use Acme\Barfoo as ZedZed;

class Definee
EOT
            ],
            'Change namespace of moved class 1' => [
                'Example1.php',
                'Acme\\Hello',
                'Acme\\Definee\\Foobar',
                <<<'EOT'
namespace Acme\Definee;

use Acme\Foobar\Warble;
use Acme\Foobar\Barfoo;
use Acme\Barfoo as ZedZed;

class Foobar
EOT
            ],
            'Change namespace of class which has same namespace as current file' => [
                'Example2.php',
                'Acme\\Barfoo',
                'Acme\\Definee\\Barfoo',
                <<<'EOT'
namespace Acme;

use Acme\Definee\Barfoo;

class Hello
{
    public function something()
    {
        Barfoo::foobar();
    }
}
EOT
            ],
            'Change namespace of long class' => [
                'Example3.php',
                'Acme\\ClassMover\\RefFinder\\RefFinder\\TolerantRefFinder',
                'Acme\\ClassMover\\Bridge\\Microsoft\\TolerantParser\\TolerantRefFinder',
                <<<'EOT'
use Acme\ClassMover\Bridge\Microsoft\TolerantParser\TolerantRefFinder;
EOT
            ],
        ];
    }
}


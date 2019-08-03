<?php

namespace Phpactor\ClassMover\Tests\Microsoft\TolerantParser;

use Microsoft\PhpParser\Parser;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantClassFinder;
use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantClassReplacer;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;

class TolerantRefRepalcerTest extends TestCase
{
    /**
     * @testdox It finds all class references.
     * @dataProvider provideTestFind
     */
    public function testFind($fileName, $classFqn, $replaceWithFqn, $expectedSource)
    {
        $parser = new Parser();
        $tolerantRefFinder = new TolerantClassFinder($parser);
        $source = SourceCode::fromString(file_get_contents(__DIR__ . '/examples/' . $fileName));
        $originalName = FullyQualifiedName::fromString($classFqn);

        $names = $tolerantRefFinder->findIn($source)->filterForName($originalName);
        $replacer = new TolerantClassReplacer();
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
            'Change namespace of interface' => [
                'Example5.php',
                'Phpactor\ClassMover\Tests\Adapter\TolerantParser\Example5Interface',
                'Phpactor\ClassMover\Tests\Adapter\TolerantParser\BarBar\FoobarInterface',
                <<<'EOT'
namespace Phpactor\ClassMover\Tests\Adapter\TolerantParser\BarBar;
EOT
            ],
            'Change namespace of trait' => [
                'Example6.php',
                'Phpactor\ClassMover\Tests\Adapter\TolerantParser\ExampleTrait',
                'Phpactor\ClassMover\Tests\Adapter\TolerantParser\BarBar\FoobarTrait',
                <<<'EOT'
namespace Phpactor\ClassMover\Tests\Adapter\TolerantParser\BarBar;
EOT
            ],
            'Change name of class expansion' => [
                'Example4.php',
                'Acme\\ClassMover\\RefFinder\\RefFinder\\TolerantRefFinder',
                'Acme\\ClassMover\\RefFinder\\RefFinder\\Foobar',
                <<<'EOT'
Foobar::class
EOT
            ],
            'Class which includes use statement for itself' => [
                'Example7.php',
                'Phpactor\ClassMover\Tests\Adapter\TolerantParser\Example7',
                'Phpactor\ClassMover\Tests\Adapter\TolerantParser\Example8',
                <<<'EOT'
class Example8
EOT
            ],
            'Self class with no namespace to a namespace' => [
                'Example8.php',
                'ClassOne',
                'Phpactor\ClassMover\Example8',
                <<<'EOT'
namespace Phpactor\ClassMover;

class Example8
{
    public function build()
    {
        return new self();
    }
}
EOT
            ],
            'Class with no namespace to a namespace' => [
                'Example9.php',
                'Example',
                'Phpactor\ClassMover\Example',
                <<<'EOT'
use Phpactor\ClassMover\Example;

class ClassOne
{
    public function build(): Example
EOT
            ],
        ];
    }
}

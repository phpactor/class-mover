<?php

namespace Phpactor\ClassMover\Tests\Adapter\WorseTolerant;

use Microsoft\PhpParser\Parser;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantClassReplaceer;
use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantClassReplacer;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;
use Phpactor\ClassMover\Domain\Reference\MethodReference;
use Phpactor\ClassMover\Domain\Reference\Position;
use Phpactor\ClassMover\Domain\Name\MethodName;
use Phpactor\ClassMover\Adapter\WorseTolerant\WorseTolerantMethodReplacer;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;

class WorseTolerantMethodReplacerTest extends WorseTolerantTestCase
{
    /**
     * @testdox It replaces all method references
     * @dataProvider provideTestReplace
     */
    public function testReplace(string $classFqn, string $methodName, string $newMethodName, string $source, string $expectedSource)
    {
        $finder = $this->createFinder($source);
        $source = SourceCode::fromString($source);

        $references = $finder->findMethods($source, ClassMethodQuery::create()->withClass($classFqn)->withMethod($methodName));

        $replacer = new WorseTolerantMethodReplacer();
        $source = $replacer->replaceMethods($source, $references, $newMethodName);
        $this->assertContains($expectedSource, $source->__toString());
    }

    public function provideTestReplace()
    {
        return [
            'It returns unmodified if no references' => [
                'Foobar', 'zzzzz', 'barfoo',
                <<<'EOT'
<?php
$foobar = new Foobar();
$foobar->foobar();
EOT
                , <<<'EOT'
<?php
$foobar = new Foobar();
$foobar->foobar();
EOT
            ],
            'It replaces references' => [
                'Foobar', 'foobar', 'barfoo',
                <<<'EOT'
<?php
class Foobar { function foobar() {} }

$foobar = new Foobar();
$foobar->foobar();
EOT
                , <<<'EOT'
$foobar->barfoo();
EOT
            ],
            'It replaces method declarations' => [
                'Foobar', 'foobar', 'barfoo',
                <<<'EOT'
<?php
class Foobar { function foobar() {} }

$foobar = new Foobar();
$foobar->foobar();
EOT
                , <<<'EOT'
class Foobar { function barfoo() {} }
EOT
            ],
        ];
    }
}

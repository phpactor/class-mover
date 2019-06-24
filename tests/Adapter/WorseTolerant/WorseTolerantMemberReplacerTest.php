<?php

namespace Phpactor\ClassMover\Tests\Adapter\WorseTolerant;

use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Adapter\WorseTolerant\WorseTolerantMemberReplacer;
use Phpactor\ClassMover\Domain\Model\ClassMemberQuery;

class WorseTolerantMemberReplacerTest extends WorseTolerantTestCase
{
    /**
     * @testdox It replaces all member references
     * @dataProvider provideTestReplace
     */
    public function testReplace(string $classFqn, string $memberName, string $newMemberName, string $source, string $expectedSource)
    {
        $finder = $this->createFinder($source);
        $source = SourceCode::fromString($source);

        $references = $finder->findMembers($source, ClassMemberQuery::create()->withClass($classFqn)->withMember($memberName));

        $replacer = new WorseTolerantMemberReplacer();
        $source = $replacer->replaceMembers($source, $references, $newMemberName);
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
            'It replaces member declarations' => [
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
            'It replaces property declarations' => [
                'Foobar', 'foobar', 'barfoo',
                <<<'EOT'
<?php
class Foobar { protected $foobar; {} }

$foobar = new Foobar();
$foobar->foobar;
EOT
                , <<<'EOT'
class Foobar { protected $barfoo; {} }
EOT
            ],
        ];
    }
}

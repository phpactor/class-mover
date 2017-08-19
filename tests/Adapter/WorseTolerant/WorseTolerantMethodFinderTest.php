<?php

namespace Phpactor\ClassMover\Tests\Adapter\WorseTolerant;

use PHPUnit\Framework\TestCase;
use Phpactor\WorseReflection\Core\SourceCodeLocator\StringSourceLocator;
use Phpactor\WorseReflection\Reflector;
use Phpactor\ClassMover\Domain\MethodFinder;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\WorseReflection\Core\SourceCode as WorseSourceCode;
use Phpactor\ClassMover\Domain\Model\ClassMethodQuery;
use Phpactor\ClassMover\Domain\Model\Class_;
use Phpactor\ClassMover\Adapter\WorseTolerant\WorseTolerantMethodFinder;
use Phpactor\ClassMover\Domain\Reference\MethodReference;
use Phpactor\ClassMover\Domain\Reference\MethodReferences;

class WorseTolerantMethodFinderTest extends TestCase
{
    /**
     * @dataProvider provideFindMethod
     */
    public function testFindMethod(string $source, ClassMethodQuery $classMethod, int $expectedCount)
    {
        $finder = $this->createFinder($source);
        $methods = $finder->findMethods(SourceCode::fromString($source), $classMethod);
        $this->assertCount($expectedCount, $methods);
    }

    public function provideFindMethod()
    {
        return [
            'It returns zero references when there are no methods at all' => [
                <<<'EOT'
<?php
class Foobar
{
}
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                0,
            ],
            'It returns zero references when there are no matching methods' => [
                <<<'EOT'
<?php
class Foobar
{
}

$foobar = new Foobar();
$foobar->barfoo();
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                0,
            ],
            'Reference for static call' => [
                <<<'EOT'
<?php
Foobar::foobar();
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                1
            ],
            'Reference for instantiated instance' => [
                <<<'EOT'
<?php
class Foobar {}

$foobar = new Foobar();
$foobar->foobar();
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                1
            ],
            'Reference for instantiated instance of wrong class' => [
                <<<'EOT'
<?php

elass Foobar { public foobar() {} }
class Barfoo {}

$foobar = new Barfoo();
$foobar->foobar();
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                0
            ],

            'Instance in method call in class' => [
                <<<'EOT'
<?php

class Beer {}

class Foobar
{
    public function hello(Beer $beer)
    {
        $beer->giveMe();
    }
}
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Beer', 'giveMe'),
                1
            ],
            'Multiple references with false positives' => [
                <<<'EOT'
<?php
class Dardar {}
class Foobar {}

$doobar = new Dardar();
$doobar->foobar();
$foobar = new Foobar();
$foobar->foobar();

($foobar->foobar())->foobar();
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                2
            ],

            'From return types' => [
                <<<'EOT'
<?php
class Goobee {}
class Foobar {}

class Foobar
{
    public function goobee(): Goobee
    {
    }
}

$foobar = new Foobar();
$foobar->goobee()->catma();

EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Goobee', 'catma'),
                1
            ],

            'Reference from parent class' => [
                <<<'EOT'
<?php

class Foobar
{
    public function foobar()
    {
    }
}

class Barfoo extends Foobar
{
}

$foobar = new Barfoo();
$foobar->foobar();

EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                1
            ],

            'Reference to interface' => [
                <<<'EOT'
<?php

interface Foobar
{
    public function foobar();
}

class Barfoo implements Foobar
{
    public function foobar()
    {
    }
}

$foobar = new Barfoo();
$foobar->foobar();

EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                1
            ],

            'Returns all methods if no method specified' => [
                <<<'EOT'
<?php

class Barfoo
{
}

$foobar = new Barfoo();
$foobar->foobar();
$foobar->bar();

EOT
                , 
                ClassMethodQuery::fromScalarClass('Barfoo'),
                2
            ],

            'Returns all methods for all classes' => [
                <<<'EOT'
<?php

class Barfoo
{
}

$foobar = new Barfoo();
$foobar->foobar();
$foobar->bar();
$stdClass = new \stdClass;
$stdClass->foobar();

EOT
                , 
                ClassMethodQuery::all(),
                3
            ],
        ];

    }

    /**
     * @dataProvider provideOffset
     */
    public function testOffset(string $source, ClassMethodQuery $classMethod, \Closure $assertion)
    {
        $finder = $this->createFinder($source);
        $methods = $finder->findMethods(SourceCode::fromString($source), $classMethod);
        $assertion(iterator_to_array($methods));
    }

    public function provideOffset()
    {
        return [
            'Start and end from static call' => [
                <<<'EOT'
<?php

Foobar::foobar();
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                function ($methods) {
                    $first = reset($methods);
                    $this->assertEquals(15, $first->position()->start());
                    $this->assertEquals(21, $first->position()->end());
                }
            ],
            'Start and end from instance call' => [
                <<<'EOT'
<?php

class Foobar () { public function foobar() {} }

$foobar = new Foobar();
$foobar->foobar();
EOT
                , 
                ClassMethodQuery::fromScalarClassAndMethodName('Foobar', 'foobar'),
                function ($methods) {
                    $first = reset($methods);
                    $this->assertEquals(89, $first->position()->start());
                    $this->assertEquals(95, $first->position()->end());
                }
            ],
        ];
    }

    private function createFinder(string $source): MethodFinder
    {
        $locator = new StringSourceLocator(WorseSourceCode::fromString($source));

        return new WorseTolerantMethodFinder(Reflector::create($locator));
    }
}

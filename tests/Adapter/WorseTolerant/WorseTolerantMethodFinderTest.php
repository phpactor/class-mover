<?php

namespace Phpactor\ClassMover\Tests\Adapter\WorseTolerant;

use PHPUnit\Framework\TestCase;
use Phpactor\WorseReflection\Core\SourceCodeLocator\StringSourceLocator;
use Phpactor\WorseReflection\Reflector;
use Phpactor\ClassMover\Domain\MethodFinder;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\WorseReflection\Core\SourceCode as WorseSourceCode;
use Phpactor\ClassMover\Domain\Model\ClassMethod;
use Phpactor\ClassMover\Domain\Model\Class_;
use Phpactor\ClassMover\Adapter\WorseTolerant\WorseTolerantMethodFinder;
use Phpactor\ClassMover\Domain\Reference\MethodReference;

class WorseTolerantMethodFinderTest extends TestCase
{
    /**
     * @dataProvider provideFindMethod
     */
    public function testFindMethod(string $source, ClassMethod $classMethod, int $expectedCount)
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
                ClassMethod::fromScalarClassAndMethodName('Foobar', 'foobar'),
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
                ClassMethod::fromScalarClassAndMethodName('Foobar', 'foobar'),
                0,
            ],
            'Reference for static call' => [
                <<<'EOT'
<?php
Foobar::foobar();
EOT
                , 
                ClassMethod::fromScalarClassAndMethodName('Foobar', 'foobar'),
                1
            ],
            'Reference for instantiated instance' => [
                <<<'EOT'
<?php

$foobar = new Foobar();
$foobar->foobar();
EOT
                , 
                ClassMethod::fromScalarClassAndMethodName('Foobar', 'foobar'),
                1
            ],
            'Reference for instantiated instance of wrong class' => [
                <<<'EOT'
<?php

$foobar = new Barfoo();
$foobar->foobar();
EOT
                , 
                ClassMethod::fromScalarClassAndMethodName('Foobar', 'foobar'),
                0
            ],

            'Instance in method call in class' => [
                <<<'EOT'
<?php

class Foobar
{
    public function hello(Beer $beer)
    {
        $beer->giveMe();
    }
}
EOT
                , 
                ClassMethod::fromScalarClassAndMethodName('Beer', 'giveMe'),
                1
            ],
            'Multiple references with false positives' => [
                <<<'EOT'
<?php

$doobar = new Dardar();
$doobar->foobar();
$foobar = new Foobar();
$foobar->foobar();

($foobar->foobar())->foobar();
EOT
                , 
                ClassMethod::fromScalarClassAndMethodName('Foobar', 'foobar'),
                2
            ],
            'From return types' => [
                <<<'EOT'
<?php

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
                ClassMethod::fromScalarClassAndMethodName('Goobee', 'catma'),
                1
            ],
        ];

    }

    private function createFinder(string $source): MethodFinder
    {
        $locator = new StringSourceLocator(WorseSourceCode::fromString($source));

        return new WorseTolerantMethodFinder(Reflector::create($locator));
    }
}

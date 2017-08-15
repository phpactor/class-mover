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

class WorseTolerantMethodFinderTest extends TestCase
{
    /**
     * @dataProvider provideFindMethod
     */
    public function testFindMethod(string $source, ClassMethod $classMethod, array $expectedReferences)
    {
        $finder = $this->createFinder($source);
        $methods = $finder->findMethods(SourceCode::fromString($source), $classMethod);
        $this->assertCount(0, $methods);
    }

    public function provideFindMethod()
    {
        return [
            'It returns zero references when there are no methods at all' => [
                <<<'EOT'
class Foobar
{
}
EOT
                , 
                ClassMethod::fromScalarClassAndMethodName('Foobar', 'foobar'),
                [],
            ],
        ];
    }

    private function createFinder(string $source): MethodFinder
    {
        $locator = new StringSourceLocator(WorseSourceCode::fromString($source));

        return new WorseTolerantMethodFinder(Reflector::create($locator));
    }
}

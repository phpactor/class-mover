<?php

namespace Phpactor\ClassMover\Tests\Adapter\WorseTolerant;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\MethodFinder;
use Phpactor\WorseReflection\Core\SourceCodeLocator\StringSourceLocator;
use Phpactor\ClassMover\Adapter\WorseTolerant\WorseTolerantMethodFinder;
use Phpactor\WorseReflection\Reflector;
use Phpactor\WorseReflection\Core\SourceCode;

abstract class WorseTolerantTestCase extends TestCase
{
    protected function createFinder(string $source): MethodFinder
    {
        $locator = new StringSourceLocator(SourceCode::fromString($source));

        return new WorseTolerantMethodFinder(Reflector::create($locator));
    }
}

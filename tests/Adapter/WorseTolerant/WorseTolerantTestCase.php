<?php

namespace Phpactor\ClassMover\Tests\Adapter\WorseTolerant;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\MemberFinder;
use Phpactor\WorseReflection\Core\SourceCodeLocator\StringSourceLocator;
use Phpactor\ClassMover\Adapter\WorseTolerant\WorseTolerantMemberFinder;
use Phpactor\WorseReflection\Reflector;
use Phpactor\WorseReflection\Core\SourceCode;

abstract class WorseTolerantTestCase extends TestCase
{
    protected function createFinder(string $source): MemberFinder
    {
        $locator = new StringSourceLocator(SourceCode::fromString($source));

        return new WorseTolerantMemberFinder(Reflector::create($locator));
    }
}

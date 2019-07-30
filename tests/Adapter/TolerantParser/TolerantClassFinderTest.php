<?php

namespace Phpactor\ClassMover\Tests\Adapter\TolerantParser;

use Microsoft\PhpParser\Parser;
use Phpactor\ClassMover\Adapter\TolerantParser\TolerantClassFinder;
use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\Model\ReferenceType;
use Phpactor\ClassMover\Domain\Reference\NamespacedClassReferences;
use Phpactor\ClassMover\Domain\SourceCode;

class TolerantClassFinderTest extends TestCase
{
    /**
     * @dataProvider provideFind
     */
    public function testFind(string $source, callable $assertion)
    {
        $parser = new Parser();
        $tolerantRefFinder = new TolerantClassFinder($parser);
        $source = SourceCode::fromString($source);
        $names = $tolerantRefFinder->findIn($source);
        $assertion($names);
    }

    public function provideFind()
    {
        yield 'example 1' => [
            file_get_contents(__DIR__ . '/examples/Example1.php'),
            function (NamespacedClassReferences $names) {
                $this->assertCount(8, $names);
                $this->assertEquals('Acme\\Foobar\\Warble', $names->at(0)->__toString());
                $this->assertEquals('Acme\\Foobar\\Barfoo', $names->at(1)->__toString());
                $this->assertEquals('Acme\\Barfoo', $names->at(2)->__toString());
                $this->assertEquals('Acme\\Hello', $names->at(3)->__toString());
                $this->assertEquals('Acme\\Foobar\\Warble', $names->at(4)->__toString());
                $this->assertEquals('Acme\\Demo', $names->at(5)->__toString());
                $this->assertEquals('Acme\\Foobar\\Barfoo', $names->at(6)->__toString());
                $this->assertEquals('Acme\\Foobar\\Barfoo', $names->at(7)->__toString());
            }
        ];

        yield 'class type' => [
            '<?php class Foobar {}',
            function (NamespacedClassReferences $names) {
                $this->assertCount(1, $names);
                $this->assertEquals(ReferenceType::CLASS(), $names->at(0)->referenceType());
            }
        ];

        yield 'interface type' => [
            '<?php interface Foobar {}',
            function (NamespacedClassReferences $names) {
                $this->assertCount(1, $names);
                $this->assertEquals(ReferenceType::INTERFACE(), $names->at(0)->referenceType());
            }
        ];

        yield 'trait type' => [
            '<?php trait Foobar {}',
            function (NamespacedClassReferences $names) {
                $this->assertCount(1, $names);
                $this->assertEquals(ReferenceType::TRAIT(), $names->at(0)->referenceType());
            }
        ];

        yield 'imported class type' => [
            '<?php use Foobar;',
            function (NamespacedClassReferences $names) {
                $this->assertCount(1, $names);
                $this->assertEquals(ReferenceType::CLASS_IMPORT(), $names->at(0)->referenceType());
            }
        ];

        yield 'qualified name' => [
            '<?php Foobar;',
            function (NamespacedClassReferences $names) {
                $this->assertCount(1, $names);
                $this->assertEquals(ReferenceType::QUALIFIED_NAME(), $names->at(0)->referenceType());
            }
        ];
    }
}

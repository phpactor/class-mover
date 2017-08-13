<?php

namespace Phpactor\ClassMover\Tests\Unit\Finder;

use PHPUnit\Framework\TestCase;
use Phpactor\ClassMover\Domain\SourceCode;
use Phpactor\ClassMover\Domain\Name\FullyQualifiedName;

class FileSourceTest extends TestCase
{
    /**
     * It should add a use statement.
     * @dataProvider provideAddUse
     */
    public function testAddUse($source, $expected)
    {
        $source = SourceCode::fromString($source);
        $source = $source->addUseStatement(FullyQualifiedName::fromString('Foobar'));
        $this->assertEquals($expected, $source->__toString());
    }

    public function provideAddUse()
    {
        return [
            'No namespace' => [
                <<<'EOT'
<?php

class
EOT
                ,
                <<<'EOT'
<?php

use Foobar;

class
EOT
            ],
            'Namespace, no use statements' => [
                <<<'EOT'
<?php

namespace Acme;

class
EOT
                ,
                <<<'EOT'
<?php

namespace Acme;

use Foobar;

class
EOT
            ],
            'Use statements' => [
                <<<'EOT'
<?php

namespace Acme;

use Acme\BarBar;

class
EOT
                ,
                <<<'EOT'
<?php

namespace Acme;

use Acme\BarBar;
use Foobar;

class
EOT
            ]
        ];
    }
}

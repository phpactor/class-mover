<?php

namespace DTL\ClassMover\Tests\Integration\Finder\Finder;

use DTL\ClassMover\Tests\Integration\IntegrationTestCase;
use DTL\ClassMover\Finder\Finder\RecursiveDirectoryFinder;
use DTL\ClassMover\Finder\SearchPath;
use DTL\ClassMover\Finder\FileList;
use DTL\ClassMover\Finder\FilePath;

class RecursiveDirectoryFinderTest extends IntegrationTestCase
{
    public function setUp()
    {
        $this->initWorkspace();
        $this->loadProject();
    }

    public function testFind()
    {
        $finder = new RecursiveDirectoryFinder();
        $fileList = $finder->findIn(SearchPath::fromString($this->workspacePath() . '/src'));

        $this->assertEquals(FileList::fromStrings([
            $this->workspacePath() . '/src/Hello/Goodbye.php',
            $this->workspacePath() . '/src/Foobar.php',
        ]), $fileList);
    }
}

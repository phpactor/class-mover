<?php

namespace DTL\ClassMover\Bridge\Symfony\Console\Command;

use DTL\ClassMover\Finder\Finder;
use DTL\ClassMover\RefFinder\RefFinder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use DTL\ClassMover\Finder\SearchPath;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use DTL\ClassMover\Finder\FileSource;
use DTL\ClassMover\RefFinder\ClassRefList;

class FindReferencesCommand extends Command
{
    private $finder;
    private $refFinder;

    public function __construct(Finder $finder, RefFinder $refFinder)
    {
        parent::__construct();
        $this->finder = $finder;
        $this->refFinder = $refFinder;
    }

    public function configure()
    {
        $this->setName('findrefs');
        $this->addArgument('fqn', InputArgument::REQUIRED, 'Fully qualified class name to find references for');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to find files in');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fileList = $this->finder->findIn(SearchPath::fromString($input->getArgument('path')));

        foreach ($fileList as $file) {
            $classRefList = $this->refFinder->findIn($file->getSource());
            $this->outputReferences($output, $classRefList);
        }
    }

    private function outputReferences(OutputInterface $output, ClassRefList $classRefList)
    {
        $output->writeln((string) $classRefList->path());
        $table = new Table($output);
        $table->setHeaders([
            'name',
            'start',
            'end',
        ]);

        foreach ($classRefList as $classRef) {
            $table->addRow([
                (string) $classRef,
                $classRef->position()->start(),
                $classRef->position()->end(),
            ]);
        }

        $table->render();
    }
}

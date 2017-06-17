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
use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\NamespacedClassRefList;
use Symfony\Component\Console\Input\InputOption;
use DTL\ClassMover\RefFinder\RefReplacer;

class FindReferencesCommand extends Command
{
    private $finder;
    private $refFinder;
    private $refReplacer;

    public function __construct(Finder $finder, RefFinder $refFinder, RefReplacer $refReplacer)
    {
        parent::__construct();
        $this->finder = $finder;
        $this->refFinder = $refFinder;
        $this->refReplacer = $refReplacer;
    }

    public function configure()
    {
        $this->setName('findrefs');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to find files in');
        $this->addArgument('fqn', InputArgument::OPTIONAL, 'Fully qualified class name to find references for');
        $this->addOption('replace', null, InputOption::VALUE_REQUIRED, 'Replace occurences');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Output changes instead of writing them');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if ($targetClass = $input->getArgument('fqn')) {
            $targetClass = FullyQualifiedName::fromString($targetClass);
        }

        if ($replaceClass = $input->getOption('replace')) {
            $replaceClass = FullyQualifiedName::fromString($replaceClass);
        }

        $fileList = $this->finder->findIn(SearchPath::fromString($input->getArgument('path')));

        foreach ($fileList as $file) {
            $classRefList = $this->refFinder->findIn($file->getSource());

            if ($targetClass) {
                $classRefList = $classRefList->filterForName($targetClass);
            }

            if ($classRefList->isEmpty()) {
                continue;
            }

            $this->outputReferences($output, $classRefList);

            if ($replaceClass) {
                $source = $this->refReplacer->replaceReferences($file->getSource(), $classRefList, $targetClass, $replaceClass);

                if ($input->getOption('dry-run')) {
                    $output->writeln($source);
                    continue;
                }

                $source->writeBackToFile();
            }
        }
    }

    private function outputReferences(OutputInterface $output, NamespacedClassRefList $classRefList)
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

<?php

namespace DTL\ClassMover;

use DTL\ClassFileConverter\ClassName as ConverterClassName;
use DTL\ClassMover\RefFinder\FullyQualifiedName;

class ClassMover
{
    private $classToFile;
    private $filesystem;

    public function __construct(ClassToFileTransformer $classToFile, Filesystem $filesystem)
    {
        $this->classToFile = $classToFile;
        $this->filesystem = $filesystem;
    }

    public function replaceReferences(SearchPath $searchPath, FullyQualifiedName $targetClas, FullyQualifiedName $newClass)
    {
        $files = $finder->findIn($searchPath);

        $referencesToReplace = [];
        foreach ($files as $file) {
            $classReferences = $this->refFinder->findClassReferences($file);

            foreach ($classReferences as $classReference) {
                if ($classReference->getFqn() === $class->getFqn()) {
                    $referencesToReplace[] = $classReference;
                }
            }

            $this->modifier->replaceClassReferences($referencesToReplace, $targetClass);
        }

        $this->classReplacer->replace($class, $targetClass, $files);
    }

    public function moveFile(ClassName $class, ClassName $targetClass)
    {
        $sourcePath = $this->classToFile($class);
        $targetPath = $this->classToFile($targetClass);

        $this->filesystem->move($sourcePath, $targetPath);
    }
}

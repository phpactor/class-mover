<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\QualifiedName;
use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\SourceNamespace;

class SourceEnvironment
{
    private $namespace;
    private $importedNames = [];

    public static function fromImportedNames(SourceNamespace $namespace, array $importedNames): SourceEnvironment
    {
        return new self($namespace, $importedNames);
    }

    public function resolveClassName(QualifiedName $name)
    {
        foreach ($this->importedNames as $importedName) {
            if ($importedName->qualifies($name)) {
                return $importedName->qualify($name);
            }
        }

        if (0 === strpos($name->__toString(), '\\')) {
            return FullyQualifiedName::fromString($name->__toString());
        }

        return $this->namespace->qualify($name);
    }

    public function namespace(): SourceNamespace
    {
        return $this->namespace;
    }

    public function isAliased(QualifiedName $name)
    {
        foreach ($this->importedNames as $importedName) {
            if ($importedName->qualifies($name)) {
                return $importedName->isAlias();
            }
        }

        return false;
    }

    private function __construct(SourceNamespace $namespace, array $importedNamespaceNames)
    {
        $this->namespace = $namespace;
        foreach ($importedNamespaceNames as $importedNamespaceName) {
            $this->addImportedName($importedNamespaceName);
        }
    }

    private function addImportedName(ImportedNamespaceName $name)
    {
        $this->importedNames[] = $name;
    }
}

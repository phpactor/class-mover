<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\QualifiedName;

class SourceEnvironment
{
    private $namespace;
    private $importedNames;

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

        return $this->namespace->qualify($name);
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

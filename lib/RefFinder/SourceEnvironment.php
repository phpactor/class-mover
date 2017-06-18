<?php

namespace DTL\ClassMover\RefFinder;

use DTL\ClassMover\RefFinder\QualifiedName;
use DTL\ClassMover\RefFinder\FullyQualifiedName;
use DTL\ClassMover\RefFinder\SourceNamespace;

class SourceEnvironment
{
    private $namespace;
    private $importedNameRefs = [];

    public static function fromImportedNameRefs(SourceNamespace $namespace, array $importedNameRefs): SourceEnvironment
    {
        return new self($namespace, $importedNameRefs);
    }

    public function resolveClassName(QualifiedName $name)
    {
        foreach ($this->importedNameRefs as $importedNameRef) {
            if ($importedNameRef->importedName()->qualifies($name)) {
                return $importedNameRef->importedName()->qualify($name);
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
        foreach ($this->importedNameRefs as $importedNameRef) {
            if ($importedNameRef->importedName()->qualifies($name)) {
                return $importedNameRef->importedName()->isAlias();
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

    private function addImportedName(ImportedNameRef $importedNameRef)
    {
        $this->importedNameRefs[] = $importedNameRef;
    }
}

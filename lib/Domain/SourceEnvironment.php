<?php

namespace Phpactor\ClassMover\Domain;

use Phpactor\ClassMover\Domain\QualifiedName;
use Phpactor\ClassMover\Domain\FullyQualifiedName;
use Phpactor\ClassMover\Domain\SourceNamespace;
use Phpactor\ClassMover\Domain\ImportedNameRef;
use Phpactor\ClassMover\Domain\SourceEnvironment;

class SourceEnvironment
{
    private $namespace;
    private $importedNameRefs = [];

    public static function fromImportedNameRefs(SourceNamespace $namespace, array $importedNameRefs): SourceEnvironment
    {
        return new self($namespace, $importedNameRefs);
    }

    public function isNameImported(QualifiedName $name)
    {
        foreach ($this->importedNameRefs as $importedNameRef) {
            if ($importedNameRef->importedName()->qualifies($name)) {
                return true;
            }
        }

        return false;
    }

    public function getImportedNameRefFor(QualifiedName $name): ImportedNameRef
    {
        foreach ($this->importedNameRefs as $importedNameRef) {
            if ($importedNameRef->importedName()->qualifies($name)) {
                return $importedNameRef;
            }
        }
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

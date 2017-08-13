<?php

namespace Phpactor\ClassMover\Domain\Name;

class Label
{
    private $methodName;

    private function __construct($methodName)
    {
        // must be a valid label: http://www.php.net/manual/en/language.variables.basics.php
        if (!preg_match('{^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$}', $methodName, $matches)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid method name (label) "%s"',
                $methodName
            ));
        }

        $this->methodName = $methodName;
    }

    public static function fromString(string $methodName): Label
    {
         return new self($methodName);
    }

    public function __toString()
    {
        return $this->methodName;
    }
}

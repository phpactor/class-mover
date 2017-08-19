<?php

namespace Phpactor\ClassMover\Domain\Name;

class Label
{
    private $label;

    private function __construct($label)
    {
        // must be a valid label: http://www.php.net/manual/en/language.variables.basics.php
        if (!preg_match('{^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$}', $label, $matches)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid method name (label) "%s"',
                $label
            ));
        }

        $this->label = $label;
    }

    public static function fromString(string $label): Label
    {
         return new static($label);
    }

    public function __toString()
    {
        return $this->label;
    }
}

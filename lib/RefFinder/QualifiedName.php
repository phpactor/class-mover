<?php

namespace DTL\ClassMover\RefFinder;

class QualifiedName
{
    protected $parts;

    protected function __construct(array $parts)
    {
        if (empty($parts)) {
            throw new \InvalidArgumentException(
                'Name cannot be empty'
            );
        }

        $this->parts = $parts;
    }

    public static function fromString(string $string)
    {
        if (empty($string)) {
            return new static([]);
        }

        $parts = explode('\\', trim($string));

        return new static($parts);
    }

    public function base()
    {
        return reset($this->parts);
    }

    public function parentNamespace(): QualifiedName
    {
        $parts = $this->parts;
        array_pop($parts);

        return new static($parts);
    }

    public function head()
    {
        return end($this->parts);
    }

    public function isAlone()
    {
        return count($this->parts) === 1;
    }

    public function __toString()
    {
        return implode('\\', $this->parts);
    }
}

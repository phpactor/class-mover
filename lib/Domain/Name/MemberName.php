<?php

namespace Phpactor\ClassMover\Domain\Name;

class MemberName extends Label
{
    public function matches(string $name)
    {
        if ((string) $this == $name) {
            return true;
        }

        if ('$' . (string) $this == $name) {
            return true;
        }

        return false;
    }
}

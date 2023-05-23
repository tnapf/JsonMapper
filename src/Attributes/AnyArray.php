<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class AnyArray implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false
    ) {
    }

    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        return true;
    }
}

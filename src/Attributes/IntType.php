<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class IntType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }

    public function isType(mixed $data): bool
    {
        return is_int($data);
    }
}

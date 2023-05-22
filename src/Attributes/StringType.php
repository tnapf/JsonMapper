<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class StringType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }

    public function isType(mixed $data): bool
    {
        return is_string($data);
    }
}

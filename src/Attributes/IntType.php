<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class IntType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function isType(mixed $data): bool
    {
        return is_int($data);
    }
}

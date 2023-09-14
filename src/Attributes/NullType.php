<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NullType implements BaseType
{
    public function __construct(public readonly string $name)
    {
    }

    public function isNullable(): bool
    {
        return true;
    }

    public function isType(mixed $data): bool
    {
        return $data === null;
    }
}

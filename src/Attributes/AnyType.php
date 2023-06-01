<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class AnyType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false
    ) {
    }

    public function isType(mixed $data): bool
    {
        return true;
    }
}

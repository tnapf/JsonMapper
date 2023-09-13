<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NullType implements BaseType
{
    public readonly bool $nullable;

    public function __construct(
        public readonly string $name
    ) {
        $this->nullable = true;
    }

    public function isType(mixed $data): bool
    {
        return $data === null;
    }
}

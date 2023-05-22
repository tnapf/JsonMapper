<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class ObjectType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false,
    ) {
    }

    public function isType(mixed $data): bool
    {
        return $data instanceof $this->class;
    }
}

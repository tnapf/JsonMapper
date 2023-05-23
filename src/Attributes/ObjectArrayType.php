<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class ObjectArrayType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false
    ) {
    }

    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $value) {
            if (!$value instanceof $this->class) {
                return false;
            }
        }

        return true;
    }
}

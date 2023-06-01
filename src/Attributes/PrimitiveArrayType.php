<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class PrimitiveArrayType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly PrimitiveType $type,
        public readonly bool $nullable = false,
    ) {
    }

    public function isType(mixed $data): bool
    {
        $method = "is_{$this->type->value}";

        foreach ($data as $value) {
            if ($method($value) === false) {
                return false;
            }
        }

        return true;
    }
}

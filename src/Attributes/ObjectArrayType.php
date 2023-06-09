<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use Tnapf\JsonMapper\Exception\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ObjectArrayType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false
    ) {
        if (!class_exists($this->class)) {
            throw new InvalidArgumentException("{$this->class} does not exist.");
        }
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

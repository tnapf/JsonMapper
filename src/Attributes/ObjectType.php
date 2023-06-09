<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use Tnapf\JsonMapper\Exception\InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ObjectType implements BaseType
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false,
    ) {
        if (!class_exists($this->class)) {
            throw new InvalidArgumentException("{$this->class} does not exist.");
        }
    }

    public function isType(mixed $data): bool
    {
        return $data instanceof $this->class;
    }
}

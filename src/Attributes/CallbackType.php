<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use Tnapf\JsonMapper\MapperInterface;

abstract class CallbackType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false
    ) {

    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    abstract public function map(mixed $data, MapperInterface $mapper): mixed;
}

<?php

namespace Tnapf\JsonMapper\Attributes;

use Tnapf\JsonMapper\MapperInterface;

abstract class MappableType implements BaseType
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

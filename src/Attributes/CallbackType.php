<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use Closure;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CallbackType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly Closure $callback,
        public readonly Closure $isTypeCallback,
        public readonly bool $nullable = false
    )
    {

    }

    public function isType(mixed $data): bool
    {
        return ($this->isTypeCallback)($data);
    }

    public function __invoke(mixed $data, MapperInterface $mapper): mixed
    {
        return ($this->callback)($data, $mapper);
    }
}
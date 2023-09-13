<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use Closure;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ArrayCallbackType implements BaseType
{
    /**
     * @param Closure $callback function (mixed $data, mixed $key, MapperInterface $mapper): Array
     * @param Closure $isTypeCallback function (mixed $data): bool
     */
    public function __construct(
        public readonly string $name,
        public readonly Closure $callback,
        public readonly Closure $isTypeCallback,
        public readonly bool $nullable = false
    ) {

    }

    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $item) {
            if (!($this->isTypeCallback)($item)) {
                return false;
            }
        }

        return true;
    }

    public function __invoke(mixed $data, MapperInterface $mapper): array
    {
        $array = [];

        foreach ($data as $key => $item) {
            $array[] = ($this->callback)($item, $key, $mapper);
        }

        return $array;
    }
}

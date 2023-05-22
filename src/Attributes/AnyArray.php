<?php

namespace Tnapf\Jsonmapper\Attributes;

use Attribute;

#[Attribute]
class AnyArray extends BaseType
{
    public function __construct(
        public readonly string $name
    ) {
    }

    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        return true;
    }
}

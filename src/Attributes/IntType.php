<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class IntType extends BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }
}

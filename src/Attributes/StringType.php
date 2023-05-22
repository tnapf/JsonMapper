<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class StringType extends BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly bool $nullable = false,
    ) {
    }
}

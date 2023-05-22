<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class ObjectType extends BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly bool $nullable = false,
    ) {
    }
}

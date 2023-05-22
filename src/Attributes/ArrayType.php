<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class ArrayType extends BaseType
{
    public const STRING = 1;
    public const INT = 2;
    public const FLOAT = 3;
    public const OBJECT = 4;
    public const BOOL = 5;

    public function __construct(
        public readonly string $name,
        public readonly ?int $type = null,
        public readonly ?string $class = null,
        public readonly bool $nullable = false,
    ) {
    }
}

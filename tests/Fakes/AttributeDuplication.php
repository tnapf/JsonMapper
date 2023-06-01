<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Attributes\PrimitiveArrayType;
use Tnapf\JsonMapper\Attributes\PrimitiveType;

class AttributeDuplication
{
    #[
        PrimitiveArrayType(name: 'property', type: PrimitiveType::INT),
        PrimitiveArrayType(name: 'property', type: PrimitiveType::FLOAT)
    ]
    public array $property;
}

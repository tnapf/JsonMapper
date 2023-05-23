<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class SnakeToCamelCase implements CaseConversionInterface
{
    public function convertToCase(string $input): string
    {
        return lcfirst(str_replace('_', '', ucwords($input, '_')));
    }

    public function convertFromCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}

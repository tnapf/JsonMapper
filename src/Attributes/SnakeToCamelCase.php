<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class SnakeToCamelCase implements CaseConversionInterface
{
    public function convertToCase(string $string): string
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    public function convertFromCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}

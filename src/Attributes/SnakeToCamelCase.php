<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;

#[Attribute]
class SnakeToCamelCase extends BaseClassAttribute
{
    public static function camelCaseToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public static function snakeCaseToCamelCase(string $input): string
    {
        return lcfirst(str_replace('_', '', ucwords($input, '_')));
    }
}

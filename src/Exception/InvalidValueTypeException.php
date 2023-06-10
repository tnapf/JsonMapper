<?php

namespace Tnapf\JsonMapper\Exception;

use Tnapf\JsonMapper\MapperException;

class InvalidValueTypeException extends MapperException
{
    public static function create(string $expected, string $actual): InvalidValueTypeException
    {
        return new InvalidValueTypeException(sprintf('Expected value of type %s, got %s', $expected, $actual));
    }
}

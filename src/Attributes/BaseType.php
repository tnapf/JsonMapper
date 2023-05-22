<?php

namespace Tnapf\JsonMapper\Attributes;

abstract class BaseType
{
    abstract public function __construct(
        string $name
    );

    abstract public function isType(mixed $data): bool;
}

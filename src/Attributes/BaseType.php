<?php

namespace Tnapf\JsonMapper\Attributes;

interface BaseType
{
    public function isType(mixed $data): bool;
}

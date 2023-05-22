<?php

namespace Tnapf\JsonMapper\Mapper;

class Mapper implements MapperInterface
{
    public function map(string $class, array $data): object
    {
        $instance = new $class();
    }
}

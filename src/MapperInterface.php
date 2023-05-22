<?php

namespace Tnapf\Spotify\Mapper;

use ReflectionException;

interface MapperInterface
{
    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @throws ReflectionException
     * @throws MapperException
     *
     * @return T
     */
    public function map(string $class, array $data): object;
}

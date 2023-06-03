<?php

namespace Tnapf\JsonMapper\Extension;

use Tnapf\JsonMapper\Mapper;
use ReflectionProperty;

abstract class PropertyExtension
{
    protected readonly Mapper $mapper;
    protected readonly ReflectionProperty $reflectionProperty;

    /**
     * @internal
     */
    public function init(Mapper $mapper, ReflectionProperty $reflectionProperty)
    {
        $this->mapper = $mapper;
        $this->reflectionProperty = $reflectionProperty;
    }

    abstract public function parseData(string $key, object &$instance, mixed &$value): void;
}

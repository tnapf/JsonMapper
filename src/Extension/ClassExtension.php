<?php

namespace Tnapf\JsonMapper\Extension;

use Tnapf\JsonMapper\Mapper;
use ReflectionClass;

abstract class ClassExtension
{
    protected readonly Mapper $mapper;
    protected readonly ReflectionClass $reflectionClass;

    /**
     * @internal
     */
    public function init(Mapper $mapper, ReflectionClass $reflectionClass)
    {
        $this->mapper = $mapper;
        $this->reflectionClass = $reflectionClass;
    }

    abstract public function parseData(object &$instance, array &$data): void;
}

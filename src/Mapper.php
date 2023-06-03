<?php

namespace Tnapf\JsonMapper;

use Tnapf\JsonMapper\Extension\ClassExtension;
use ReflectionAttribute;
use ReflectionClass;

class Mapper
{
    /**
     * @template T
     *
     * @var string-class<T>
     *
     * @return T
     */
    public function map(string $class, array $data)
    {
        $reflectionClass = new ReflectionClass($class);

        $instance = new $class();
        $classExtensions = $reflectionClass->getAttributes(ClassExtension::class, ReflectionAttribute::IS_INSTANCEOF);

        foreach ($classExtensions as $extension) {
            $extensionInstance = $extension->newInstance();

            $extensionInstance->init($this, $reflectionClass);
            $extensionInstance->parseData($instance, $data);
        }

        return $instance;
    }
}

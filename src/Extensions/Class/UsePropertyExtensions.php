<?php

namespace Tnapf\JsonMapper\Extensions\Class;

use Attribute;
use Tnapf\JsonMapper\Extension\ClassExtension;
use Tnapf\JsonMapper\Extension\PropertyExtension;
use ReflectionAttribute;
use ReflectionException;

#[Attribute()]
class UsePropertyExtensions extends ClassExtension
{
    public function parseData(object &$instance, array &$data): void
    {
        foreach ($data as $key => &$value) {
            try {
                $reflectionProperty = $this->reflectionClass->getProperty($key);
            } catch (ReflectionException) {
                continue;
            }

            $propertyExtensions = $reflectionProperty->getAttributes(PropertyExtension::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($propertyExtensions as $extension) {
                $extensionInstance = $extension->newInstance();

                $extensionInstance->init($this->mapper, $reflectionProperty);
                $extensionInstance->parseData($key, $instance, $value);
            }
        }
    }
}

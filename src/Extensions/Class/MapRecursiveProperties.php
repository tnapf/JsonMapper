<?php

namespace Tnapf\JsonMapper\Extensions\Class;

use Attribute;
use Tnapf\JsonMapper\Extensions\Class\Exceptions\PropertyMapper\NoValueException;
use ReflectionNamedType;
use ReflectionProperty;

#[Attribute()]
class MapRecursiveProperties extends PropertyMapper
{
    protected function getPropertiesToMap(): array
    {
        return array_filter(
            parent::getPropertiesToMap(),
            function (ReflectionProperty $reflectionProperty) {
                $type = $reflectionProperty->getType();

                $types = $type instanceof ReflectionNamedType ? [$type] : $type->getTypes();

                foreach ($types as $singleType) {
                    if (class_exists($singleType)) {
                        return true;
                    }
                }

                return false;
            }
        );
    }

    protected function getValue(ReflectionProperty $property, mixed $rawValue): mixed
    {
        if (!is_array($rawValue)) {
            throw new NoValueException();
        }

        $type = $property->getType();

        $types = $type instanceof ReflectionNamedType ? [$type] : $type->getTypes();

        foreach ($types as $singleType) {
            $class = $singleType->getName();
            if (class_exists($class)) {
                return $this->mapper->map($class, $rawValue);
            }
        }

        throw new NoValueException();
    }
}

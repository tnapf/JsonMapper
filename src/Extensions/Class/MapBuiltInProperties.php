<?php

namespace Tnapf\JsonMapper\Extensions\Class;

use Attribute;
use ReflectionNamedType;
use ReflectionProperty;

#[Attribute()]
class MapBuiltInProperties extends PropertyMapper
{
    protected function getPropertiesToMap(): array
    {
        return array_filter(
            parent::getPropertiesToMap(),
            function (ReflectionProperty $reflectionProperty) {
                $type = $reflectionProperty->getType();

                $types = $type instanceof ReflectionNamedType ? [$type] : $type->getTypes();

                foreach ($types as $singleType) {
                    if ($singleType->isBuiltin()) {
                        return true;
                    }
                }

                return false;
            }
        );
    }

    protected function getValue(ReflectionProperty $property, mixed $rawValue): mixed
    {
        return $rawValue;
    }
}

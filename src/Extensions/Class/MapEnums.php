<?php

namespace Tnapf\JsonMapper\Extensions\Class;

use Attribute;
use Tnapf\JsonMapper\Extensions\Class\Exceptions\PropertyMapper\NoValueException;
use ReflectionNamedType;
use ReflectionProperty;
use ValueError;

#[Attribute()]
class MapEnums extends PropertyMapper
{
    protected function getPropertiesToMap(): array
    {
        return array_filter(
            parent::getPropertiesToMap(),
            function (ReflectionProperty $reflectionProperty) {
                $type = $reflectionProperty->getType();

                $types = $type instanceof ReflectionNamedType ? [$type] : $type->getTypes();

                foreach ($types as $singleType) {
                    if (enum_exists($singleType->getName())) {
                        return true;
                    }
                }

                return false;
            }
        );
    }

    protected function getValue(ReflectionProperty $property, mixed $rawValue): mixed
    {
        $type = $property->getType();

        $types = $type instanceof ReflectionNamedType ? [$type] : $type->getTypes();

        foreach ($types as $singleType) {
            $enumName = $singleType->getName();
            if (enum_exists($enumName)) {
                try {
                    return $enumName::from($rawValue);
                } catch (ValueError) {
                    continue;
                }
            }
        }

        throw new NoValueException();
    }
}

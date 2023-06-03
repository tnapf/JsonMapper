<?php

namespace Tnapf\JsonMapper\Extensions\Property;

use Attribute;
use Tnapf\JsonMapper\Extension\PropertyExtension;
use Tnapf\JsonMapper\Extensions\Class\Exceptions\PropertyMapper\NoValueException;

#[Attribute()]
class MapArray extends PropertyExtension
{
    /**
     * @param class-string $class
     */
    public function __construct(private readonly string $class)
    {
    }

    public function parseData(string $key, object &$instance, mixed &$value): void
    {
        if (!is_array($value)) {
            throw new NoValueException();
        }

        $instance->{$key} = [];
        foreach ($value as $itemKey => $itemRaw) {
            $instance->{$key}[$itemKey] = $this->mapper->map($this->class, $itemRaw);
        }
    }
}

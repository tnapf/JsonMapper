<?php

namespace Tnapf\JsonMapper\Extensions\Class;

use Attribute;
use Tnapf\JsonMapper\Extension\ClassExtension;
use Tnapf\JsonMapper\Extensions\Class\Exceptions\PropertyMapper\NoValueException;
use ReflectionProperty;

#[Attribute()]
abstract class PropertyMapper extends ClassExtension
{
    /**
     * @see https://www.php.net/manual/en/class.reflectionproperty.php#reflectionproperty.constants.modifiers
     */
    public function __construct(protected readonly ?int $filter = null)
    {
    }

    /**
     * @return ReflectionProperty[]
     */
    protected function getPropertiesToMap(): array
    {
        return $this->reflectionClass->getProperties($this->filter);
    }

    abstract protected function getValue(ReflectionProperty $property, mixed $rawValue): mixed;

    public function parseData(object &$instance, array &$data): void
    {
        $toMap = $this->getPropertiesToMap();

        foreach ($toMap as $property) {
            $name = $property->getName();

            if ($property->isInitialized($instance) || !array_key_exists($name, $data)) {
                continue;
            }

            try {
                $property->setValue($instance, $this->getValue($property, $data[$name]));
            } catch (NoValueException) {
                continue;
            }
        }
    }
}

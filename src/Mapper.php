<?php

namespace Tnapf\JsonMapper;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Tnapf\Jsonmapper\Attributes\AnyArray;
use Tnapf\JsonMapper\Attributes\BaseType;
use Tnapf\JsonMapper\Attributes\BoolType;
use Tnapf\JsonMapper\Attributes\IntType;
use Tnapf\JsonMapper\Attributes\ObjectType;
use Tnapf\JsonMapper\Attributes\SnakeToCamelCase;
use Tnapf\JsonMapper\Attributes\StringType;

class Mapper implements MapperInterface
{
    protected bool $snakeToCamelCase = false;
    protected object $instance;
    protected ReflectionClass $reflection;

    /**
     * @var BaseType[]
     */
    protected array $attributes;

    protected string $class;
    protected array $data;

    public function map(string $class, array $data): object
    {
        $instance = (new self());

        $instance->class = $class;
        $instance->data = $data;
        $instance->reflection = new ReflectionClass($class);
        $instance->snakeToCamelCase = !empty($instance->reflection->getAttributes(SnakeToCamelCase::class));
        $instance->instance = new $class();

        return $instance->doMapping();
    }

    protected function doMapping(): object
    {
        $this->fillPropertyAttributes();

        foreach ($this->attributes as $types) {
            $attribute = $types[0];
            $data = $this->data[$attribute->name] ?? null;

            if ($data === null) {
                if ($attribute->isNullable()) {
                    continue;
                }

                throw new MapperException("Property {$attribute->getName()} is not nullable");
            }

            $validType = false;
            foreach ($types as $attribute) {
                if ($attribute->isType($data)) {
                    $validType = true;

                    if ($attribute instanceof ObjectType) {
                        $data = (new self())->map($attribute->class, $data);
                    }

                    break;
                }

            }

            if (!$validType) {
                throw new MapperException("Property {$attribute->name} is not of type ".implode(', ', array_map(static fn ($type) => $type::class, $types)));
            }

            $this->instance->{$this->snakeToCamelCase ? SnakeToCamelCase::snakeCaseToCamelCase($attribute->name) : $attribute->name} = $data;
        }

        return $this->instance;
    }

    protected function fillPropertyAttributes(): void
    {
        $properties = $this->reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $name = $this->snakeToCamelCase ? SnakeToCamelCase::camelCaseToSnakeCase($property->getName()) : $property->getName();
            $attributes = $property->getAttributes(BaseType::class, ReflectionAttribute::IS_INSTANCEOF);
            $this->attributes[$name] = [];

            if ($attributes !== []) {
                $this->attributes[$name] = [...$this->attributes[$name], ...$attributes];

                continue;
            }

            if (method_exists($property->getType(), 'getTypes')) {
                $types = $property->getType()->getTypes();
            } else {
                $types = [$property->getType()];
            }

            if ($types === null) {
                throw new MapperException("Property {$property->getName()} has no type");
            }

            foreach ($types as $type) {
                $this->attributes[$property->getName()][] = match ($type->getName()) {
                    'int' => new IntType($name),
                    'bool' => new BoolType($name),
                    'string' => new StringType($name),
                    'array' => new AnyArray($name),
                    default => new ObjectType($name, $type->getName()),
                };
            }
        }
    }
}

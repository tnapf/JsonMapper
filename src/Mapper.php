<?php

namespace Tnapf\JsonMapper;

use ReflectionAttribute;
use ReflectionClass;
use Tnapf\JsonMapper\Attributes\AnyArray;
use Tnapf\JsonMapper\Attributes\AnyType;
use Tnapf\JsonMapper\Attributes\CaseConversionInterface;
use Tnapf\JsonMapper\Attributes\BaseType;
use Tnapf\JsonMapper\Attributes\BoolType;
use Tnapf\JsonMapper\Attributes\FloatType;
use Tnapf\JsonMapper\Attributes\IntType;
use Tnapf\JsonMapper\Attributes\ObjectArrayType;
use Tnapf\JsonMapper\Attributes\ObjectType;
use Tnapf\JsonMapper\Attributes\StringType;

class Mapper implements MapperInterface
{
    protected ?CaseConversionInterface $caseConversion = null;
    protected object $instance;
    protected ReflectionClass $reflection;

    /**
     * @var BaseType[]
     */
    protected array $attributes;

    /**
     * @var array <class-string, BaseType[]>
     */
    protected static array $attributesCache = [];

    protected string $class;
    protected array $data;

    public function map(string $class, array $data): object
    {
        $instance = new self();
        $instance->class = $class;
        $instance->data = $data;
        $instance->reflection = new ReflectionClass($class);

        if ($attributes = $instance->reflection->getAttributes(CaseConversionInterface::class, ReflectionAttribute::IS_INSTANCEOF)) {
            if (count($attributes) > 1) {
                throw new MapperException("{$class} has more than one case conversion attribute");
            }

            $instance->caseConversion = $attributes[0]->newInstance();
        }

        $instance->attributes = self::$attributesCache[$class] ?? [];
        $instance->instance = $instance->reflection->newInstanceWithoutConstructor();

        $object = $instance->doMapping();

        if (!isset(self::$attributesCache[$class])) {
            self::$attributesCache[$class] = $instance->attributes;
        }

        return $object;
    }

    protected function convertNameToCase(string $name): string
    {
        return $this?->caseConversion?->convertToCase($name) ?? $name;
    }

    protected function convertNameFromCase(string $name): string
    {
        return $this?->caseConversion?->convertFromCase($name) ?? $name;
    }

    protected function doMapping(): object
    {
        $this->fillPropertyAttributes();

        foreach ($this->attributes as $types) {
            $attribute = $types[0];
            $data = $this->data[$this->convertNameFromCase($attribute->name)] ?? null;

            if ($data === null) {
                if ($attribute->nullable) {
                    continue;
                }

                throw new MapperException("Property {$attribute->name} is not nullable");
            }

            $validType = false;

            foreach ($types as $attribute) {
                if ($attribute instanceof ObjectType) {
                    $data = $this->map($attribute->class, $data);
                }

                if ($attribute instanceof ObjectArrayType) {
                    $data = array_map(
                        fn ($item) => $this->map($attribute->class, $item),
                        $data
                    );
                }

                if ($attribute->isType($data)) {
                    $validType = true;
                    break;
                }
            }

            if (!$validType) {
                throw new MapperException(
                    "Property {$attribute->name} is not of type ".
                    implode(
                        ', ',
                        array_map(static fn ($type) => $type::class, $types)
                    )
                );
            }

            $camelCasePropertyName = $this->convertNameToCase($attribute->name);
            $property = $this->reflection->getProperty($camelCasePropertyName);
            $property->setValue($this->instance, $data);
        }

        return $this->instance;
    }

    protected function fillPropertyAttributes(): void
    {
        if ($this->attributes !== []) {
            return;
        }

        $properties = $this->reflection->getProperties();

        foreach ($properties as $property) {
            $attributes = array_map(
                static fn (ReflectionAttribute $attribute) => $attribute->newInstance(),
                $property->getAttributes(BaseType::class, ReflectionAttribute::IS_INSTANCEOF)
            );

            $name = $property->getName();
            $type = $property->getType();
            $this->attributes[$name] = [];

            if ($attributes !== []) {
                $this->attributes[$name] = [...$this->attributes[$name], ...$attributes];

                continue;
            }

            if ($type === null) {
                $this->attributes[$name][] = new AnyType($name);

                continue;
            }

            $types = method_exists($type, 'getTypes') ?
                $type->getTypes() :
                [$type];

            foreach ($types as $type) {
                $this->attributes[$name][] = match ($type->getName()) {
                    'int' => new IntType(name: $name, nullable: $type->allowsNull()),
                    'bool' => new BoolType(name: $name, nullable: $type->allowsNull()),
                    'string' => new StringType(name: $name, nullable: $type->allowsNull()),
                    'array' => new AnyArray(name: $name, nullable: $type->allowsNull()),
                    'float' => new FloatType(name: $name, nullable: $type->allowsNull()),
                    default => new ObjectType(name: $name, class: $type->getName(), nullable: $type->allowsNull()),
                };
            }
        }
    }
}

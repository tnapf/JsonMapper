<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use BackedEnum;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionException;
use Tnapf\JsonMapper\Tests\Fakes\RolePermission;
use UnitEnum;


#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumerationType implements BaseType
{
    /** @var class-string<UnitEnum> $enum */
    public function __construct(
        public readonly string $name,
        public readonly string $enum,
        public readonly bool $caseSensitive = true,
        public readonly bool $nullable = false
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function isType(mixed $data): bool
    {
        $isScalar = is_int($data) || is_string($data);
        $isEnumValue = $data instanceof UnitEnum;

        if (!$isScalar && !$isEnumValue) {
            return false;
        }

        $reflector = new ReflectionEnum($this->enum);

        $comparator = $this->caseSensitive ? strcmp(...) : strcasecmp(...);
        foreach ($reflector->getCases() as $case) {
            if ($isEnumValue && $case->getValue() === $data) {
                return true;
            }

            if ($isScalar && $reflector->isBacked() && $comparator($case->getBackingValue(), $data) === 0) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use InvalidArgumentException;
use ReflectionEnum;
use ReflectionException;
use UnitEnum;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumerationType implements BaseType
{
    public function __construct(
        public readonly string $name,
        public readonly string $enum,
        public readonly bool $caseSensitive = true,
        public readonly bool $nullable = false
    ) {
        if (!enum_exists($this->enum)) {
            throw new InvalidArgumentException('Enumeration does not exist or is invalid.');
        }
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

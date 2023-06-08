<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use BackedEnum;
use InvalidArgumentException;
use ReflectionEnum;
use Tnapf\JsonMapper\Exception\InvalidValueTypeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumerationArrayType implements BaseType
{
    private ReflectionEnum $reflector;

    public function __construct(
        public readonly string $name,
        public readonly string $enum,
        public readonly bool $caseSensitive = true,
        public readonly bool $nullable = false
    ) {
        if (!enum_exists($this->enum)) {
            throw new InvalidArgumentException('Enumeration does not exist or is invalid.');
        }

        $this->reflector = new ReflectionEnum($this->enum);
        if (!$this->reflector->isBacked()) {
            throw new InvalidArgumentException('Non-backed enumerations cannot be mapped.');
        }
    }

    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        $cases = $this->reflector->getCases();
        foreach ($data as $item) {
            foreach ($cases as $case) {
                if ($case->getValue() === $item) {
                    continue 2;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * @throws InvalidValueTypeException
     *
     * @return array<array-key, BackedEnum|null>
     */
    public function convert(mixed $data): array
    {
        if (!is_array($data)) {
            throw new InvalidValueTypeException('array', gettype($data));
        }

        if ($this->caseSensitive) {
            return array_map($this->enum::tryFrom(...), $data);
        }

        return array_map(
            function (mixed $item): ?BackedEnum {
                foreach ($this->reflector->getCases() as $case) {
                    if (strcasecmp($case->getBackingValue(), $item) === 0) {
                        return $case->getValue();
                    }
                }

                return null;
            },
            $data
        );
    }
}

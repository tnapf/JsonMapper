<?php

namespace Tnapf\JsonMapper\Attributes;

use Attribute;
use BackedEnum;
use ReflectionEnum;
use ReflectionException;
use Tnapf\JsonMapper\Exception\InvalidArgumentException;
use Tnapf\JsonMapper\Exception\InvalidValueTypeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EnumerationType implements BaseType
{
    /** @var class-string<BackedEnum> */
    public readonly string $enum;

    private ReflectionEnum $reflector;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        public readonly string $name,
        string $enum,
        public readonly bool $caseSensitive = true,
        public readonly bool $nullable = false
    ) {
        $this->enum = $enum;
        if (!enum_exists($this->enum)) {
            throw new InvalidArgumentException('Enumeration does not exist or is invalid.');
        }

        $this->reflector = new ReflectionEnum($this->enum);
        if (!$this->reflector->isBacked()) {
            throw new InvalidArgumentException('Non-backed enumerations cannot be mapped.');
        }
    }

    /**
     * @throws ReflectionException
     * @throws InvalidValueTypeException
     */
    public function isType(mixed $data): bool
    {
        foreach ($this->reflector->getCases() as $case) {
            if ($case->getValue() === $data) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws InvalidValueTypeException
     */
    public function convert(mixed $data): ?BackedEnum
    {
        if (!is_string($data) && !is_int($data)) {
            throw InvalidValueTypeException::create('int or string', gettype($data));
        }

        if ($this->caseSensitive) {
            return $this->enum::tryFrom($data);
        }

        foreach ($this->reflector->getCases() as $case) {
            if (strcasecmp($case->getBackingValue(), $data) === 0) {
                return $case->getValue();
            }
        }

        return null;
    }
}

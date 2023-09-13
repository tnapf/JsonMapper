<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Attribute;
use ReflectionException;
use Tnapf\JsonMapper\Attributes\CallbackType;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FriendsType extends CallbackType
{
    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $item) {
            if (!$item instanceof User) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws ReflectionException
     * @throws MapperException
     */
    public function map(mixed $data, MapperInterface $mapper): mixed
    {
        $friends = [];

        foreach ($data as $item) {
            $friend = $mapper->map(User::class, $item);
            $friends[$friend->username] = $friend;
        }

        return $friends;
    }
}

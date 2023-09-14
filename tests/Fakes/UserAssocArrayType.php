<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Attribute;
use ReflectionException;
use Tnapf\JsonMapper\Attributes\MappableType;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UserAssocArrayType extends MappableType
{
    public function isType(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $key => $item) {
            if (!$item instanceof User) {
                return false;
            }

            if ($key !== $item->username) {
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
        $users = [];

        foreach ($data as $userArray) {
            $user = $mapper->map(User::class, $userArray);
            $users[$user->username] = $user;
        }

        return $users;
    }
}

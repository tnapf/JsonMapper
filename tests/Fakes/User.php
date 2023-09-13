<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Attributes\ObjectArrayType;

class User
{
    public int $id;
    public string $username;
    public string $password;

    #[ObjectArrayType(name: 'roles', class: Role::class)]
    public array $roles;

    #[FriendsType(name: 'friends', nullable: true)]
    public array $friends;

    public Address $address;
}

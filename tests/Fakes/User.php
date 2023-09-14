<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Attributes\ObjectArrayType;

class User
{
    public int $id;
    public string $username;
    public string $password;
    public ?Address $address;

    #[ObjectArrayType(name: 'roles', class: Role::class, nullable: true)]
    public array $roles;

    #[UserAssocArrayType(name: 'friends', nullable: true)]
    public array $friends;
}

<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Attributes\EnumerationType;

class Role
{
    public int $id;
    public string $name;

    #[EnumerationType('permissions', RolePermission::class)]
    public RolePermission $permissions;
}

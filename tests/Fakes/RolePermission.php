<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

enum RolePermission: int
{
    case READ = 1;
    case WRITE = 2;
    case UNRESTRICTED = 4;
}

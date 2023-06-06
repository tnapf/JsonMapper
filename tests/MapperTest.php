<?php

namespace Tnapf\JsonMapper\Tests;

use PHPUnit\Framework\TestCase;
use Tnapf\JsonMapper\Mapper;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;
use Tnapf\JsonMapper\Tests\Fakes\Address;
use Tnapf\JsonMapper\Tests\Fakes\AnyTypes;
use Tnapf\JsonMapper\Tests\Fakes\Role;
use Tnapf\JsonMapper\Tests\Fakes\RolePermission;
use Tnapf\JsonMapper\Tests\Fakes\User;

class MapperTest extends TestCase
{
    protected MapperInterface $mapper;

    public function getMapper()
    {
        $this->mapper ??= new Mapper();

        return $this->mapper;
    }

    public function testMapping()
    {
        $user = [
            'id' => 1,
            'username' => ':username:',
            'password' => ':password:',
            'roles' => [
                [
                    'id' => 0,
                    'name' => ':role1:',
                    'permissions' => 1,
                ],
                [
                    'id' => 1,
                    'name' => ':role2:',
                    'permissions' => 2,
                ],
            ],
            'address' => [
                'street' => ':street:',
                'city' => ':city:',
                'country' => ':country:',
                'zip' => 1111,
                'latitude_degrees' => 1.1,
                'longitude_degrees' => 2.2,
            ],
        ];

        $mappedUser = $this->getMapper()->map(User::class, $user);

        $this->assertInstanceOf(User::class, $mappedUser);
        $this->assertSame($user['id'], $mappedUser->id);
        $this->assertSame($user['username'], $mappedUser->username);
        $this->assertSame($user['password'], $mappedUser->password);

        foreach ($mappedUser->roles as $key => $role) {
            $this->assertInstanceOf(Role::class, $role);
            $this->assertSame($user['roles'][$key]['id'], $role->id);
            $this->assertSame($user['roles'][$key]['name'], $role->name);

            $this->assertInstanceOf(RolePermission::class, $role->permissions);
            $this->assertSame($user['roles'][$key]['permissions'], $role->permissions->value);
        }

        $this->assertSame($user['address']['street'], $mappedUser->address->street);
        $this->assertSame($user['address']['city'], $mappedUser->address->city);
        $this->assertSame($user['address']['country'], $mappedUser->address->country);
        $this->assertSame($user['address']['zip'], $mappedUser->address->zip);
        $this->assertSame($user['address']['latitude_degrees'], $mappedUser->address->latitudeDegrees);
        $this->assertSame($user['address']['longitude_degrees'], $mappedUser->address->longitudeDegrees);
    }

    public function testMultipleTypeMapping()
    {
        $address1 = [
            'street' => ':street:',
            'city' => ':city:',
            'country' => ':country:',
            'zip' => 1111,
            'latitude_degrees' => 1.1,
            'longitude_degrees' => 2.2,
        ];

        $address2 = [
            'street' => ':street:',
            'city' => ':city:',
            'country' => ':country:',
            'zip' => '1111',
            'latitude_degrees' => 1.1,
            'longitude_degrees' => 2.2,
        ];

        $mappedAddress1 = $this->getMapper()->map(Address::class, $address1);
        $mappedAddress2 = $this->getMapper()->map(Address::class, $address2);

        $this->assertSame($address1['zip'], $mappedAddress1->zip);
        $this->assertSame($address2['zip'], $mappedAddress2->zip);
    }

    public function testWrongType()
    {
        $this->expectException(MapperException::class);

        $address = [
            'street' => 1234,
            'city' => ':city:',
            'country' => ':country:',
            'zip' => 1111,
            'latitude_degrees' => 1.1,
            'longitude_degrees' => 2.2,
        ];

        $this->getMapper()->map(Address::class, $address);
    }

    public function testNullableProperty()
    {
        $address = [
            'street' => ':street;',
            'city' => ':city:',
            'country' => ':country:',
            'zip' => 1111,
        ];

        $mappedAddress = $this->getMapper()->map(Address::class, $address);

        $this->assertFalse(isset($mappedAddress->latitudeDegrees));
        $this->assertFalse(isset($mappedAddress->longitudeDegrees));
    }

    public function testMissingProperty()
    {
        $this->expectException(MapperException::class);

        $address = [
            'street' => ':street;',
            'city' => ':city:',
            'country' => ':country:',
            'latitude_degrees' => 1.1,
            'longitude_degrees' => 2.2,
        ];

        $this->getMapper()->map(Address::class, $address);
    }

    public function testAnyTypes()
    {
        $anyTypes1 = [
            'anyType' => 1,
        ];

        $anyTypes2 = [
            'anyType' => ':value:',
        ];

        $mappedAnyTypes1 = $this->getMapper()->map(AnyTypes::class, $anyTypes1);
        $mappedAnyTypes2 = $this->getMapper()->map(AnyTypes::class, $anyTypes2);

        $this->assertSame($anyTypes1['anyType'], $mappedAnyTypes1->anyType);
        $this->assertSame($anyTypes2['anyType'], $mappedAnyTypes2->anyType);
    }
}

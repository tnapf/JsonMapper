# Tnapf/JsonMapper

A JSON Mapper

# Installation

```bash
composer require tnapf/json-mapper
```

# Supported Types

* Enums
* Primitives
* Objects
* Arrays
* Custom Types (via CallbackType)

# Usage

## Instantiate Mapper

```php
use Tnapf\JsonMapper\Mapper;

$mapper = new Mapper;
```

## Create an abstraction class

```php
class User
{
    public int $id;
    public string $username;
    public string $password;
}
```

## Convert JSON to array and map the class
```php
$user = [
    "id" => 1,
    "username" => ":username:",
    "password" => ":password:"
];

$mappedUser = $mapper->map($user, User::class);
```

## Typing with Attributes

### Primitive Types

For primitive types you can use the `PrimitiveArray` attribute on the property

```php
use Tnapf\JsonMapper\Attributes\PrimitiveType;
use Tnapf\JsonMapper\Attributes\PrimitiveArrayType;

class User
{
    public int $id;
    public string $username;
    public string $password;
    
    #[PrimitiveArrayType(name: 'roles', type: PrimitiveType::STRING)]
    public array $roles;
}
```

```php
// what the new item will look like
$user = [
    // ...
    'roles' => [':name:', ':name:', ':name:']
];
```

### Object Types

If you want the array to have a class, you can use the ObjectArrayType attribute

```php
use Tnapf\JsonMapper\Attributes\ObjectArrayType;

class User
{
    public int $id;
    public string $username;
    public string $password;
    
    #[ObjectArrayType(name: 'roles', type: Role::class)]
    public array $roles;
}

class Role {
    public int $id;
    public string $name;
}
```
```php
// what the updated item will look like
$user = [
    // ...
    'roles' => [
        [
            'id' => 1,
            'name' => ':name:'
        ],
        [
            'id' => 2,
            'name' => ':name:'
        ],
        [
            'id' => 3,
            'name' => ':name:'
        ]
    ]
];
```

### Array Enumeration Types

```php
use Tnapf\JsonMapper\Attributes\EnumerationArrayType;

class User {
    public int $id;
    public string $username;
    public string $password;
    
    #[EnumerationArrayType(name: 'roles', type: Role::class))]
    public array $roles;
}

enum Role: int
{
    case USER = 1;
    case ADMIN = 2;
    case OWNER = 3;
}
```
```php
// what the updated item will look like
$user = [
    // ...
    'roles' => [1, 3]
];
```

### CallbackType
   
If you need to do something specific to map a class, you can extend the CallbackType class to create a custom type

```php
use Attribute;
use ReflectionException;
use Tnapf\JsonMapper\Attributes\MappableType;
use Tnapf\JsonMapper\MapperException;
use Tnapf\JsonMapper\MapperInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FriendsType extends MappableType
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

class User {
    public string $username;
    public string $password;
    
    #[FriendsType(name: 'friends', nullable: true)]
    public array $friends;
}
```
```php
// what the updated item will look like
$user = [
    // ...
    'friends' => [
        [
            'username' => ':username:',
            'password' => ':password:'
        ],
        [
            'username' => ':username:',
            'password' => ':password:'
        ],
        [
            'username' => ':username:',
            'password' => ':password:'
        ]
    ]
];
```

## Property Case Conversion

Since the common json naming convention is `snake_case` and PHP's is `camelCase` you can use apply an attribute to the class to have the `snake_case` json properties routed to your `camelCase` properties.

```php
use Tnapf\JsonMapper\Attributes\SnakeToCamelCase;
use Tnapf\JsonMapper\Attributes\PrimitiveType;
use Tnapf\JsonMapper\Attributes\PrimitiveArrayType;

#[SnakeToCamelCase]
class User
{
    public int $id;
    public string $username;
    public string $password;
    
    #[PrimitiveArrayType(name: 'all_roles', type: PrimitiveType::STRING)]
    public array $allRoles;
}
```

While allRoles is `camelCase` in the class you can see the JSON below uses `snake_case`

```json
{
    "id": 1,
    "username": ":username:",
    "password": "1234",
    "all_roles": [
        "admin",
        "user"
    ]
}
```

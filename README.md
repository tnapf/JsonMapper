# Tnapf/JsonMapper

A JSON Mapper (still in development)

# Installation

```bash
composer require tnapf/json-mapper
```

# Usage

## Instantiate Mapper

```php
use Tnapf\JsonMapper\Mapper;

$mapper = new Mapper;
```

## Create a abstraction class

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

## Array Typing

### PrimitiveTypes

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
    public array $colors;
}
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

While allRoles is `camelCase` you can see the JSON below uses `snake_case`

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

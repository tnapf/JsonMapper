<?php

namespace Tnapf\JsonMapper\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Tnapf\JsonMapper\Attributes\AnyArray;
use Tnapf\JsonMapper\Attributes\AnyType;
use Tnapf\JsonMapper\Attributes\BoolType;
use Tnapf\JsonMapper\Attributes\EnumerationArrayType;
use Tnapf\JsonMapper\Attributes\EnumerationType;
use Tnapf\JsonMapper\Attributes\ObjectArrayType;
use Tnapf\JsonMapper\Attributes\ObjectType;
use Tnapf\JsonMapper\Attributes\FloatType;
use Tnapf\JsonMapper\Attributes\PrimitiveType;
use Tnapf\JsonMapper\Attributes\IntType;
use Tnapf\JsonMapper\Attributes\PrimitiveArrayType;
use Tnapf\JsonMapper\Attributes\StringType;
use Tnapf\JsonMapper\Exception\InvalidArgumentException;
use Tnapf\JsonMapper\Tests\Fakes\IssueCategory;
use Tnapf\JsonMapper\Tests\Fakes\IssueState;
use Tnapf\JsonMapper\Tests\Fakes\RolePermission;
use Tnapf\JsonMapper\Tests\Fakes\AttributeDuplication;

class AttributeTest extends TestCase
{
    public function testAnyArray()
    {
        $anyArray = new AnyArray(name: 'anyArray');

        $this->assertTrue($anyArray->isType(['test', 1, 1.52]));
        $this->assertFalse($anyArray->isType('test'));
    }

    public function testAnyType()
    {
        $anyType = new AnyType(name: 'anyType');

        $this->assertTrue($anyType->isType('test'));
        $this->assertTrue($anyType->isType(1));
    }

    public function testBoolType()
    {
        $boolType = new BoolType(name: 'boolType');

        $this->assertTrue($boolType->isType(true));
        $this->assertTrue($boolType->isType(false));
        $this->assertFalse($boolType->isType('test'));
    }

    public function testFloatType()
    {
        $floatType = new FloatType(name: 'floatType');

        $this->assertTrue($floatType->isType(1.52));
        $this->assertFalse($floatType->isType('test'));
    }

    public function testIntType()
    {
        $intType = new IntType(name: 'intType');

        $this->assertTrue($intType->isType(1));
        $this->assertFalse($intType->isType('test'));
    }

    public function testObjectArray()
    {
        $objectArray = new ObjectArrayType(name: 'objectArray', class: stdClass::class);

        $this->assertTrue($objectArray->isType([new stdClass(), new stdClass()]));
        $this->assertFalse($objectArray->isType(['test']));
        $this->assertFalse($objectArray->isType('test'));
    }

    public function testObjectType()
    {
        $objectType = new ObjectType(name: 'objectType', class: stdClass::class);

        $this->assertTrue($objectType->isType(new stdClass()));
        $this->assertFalse($objectType->isType('test'));
    }

    public function testStringType()
    {
        $stringType = new StringType(name: 'stringType');

        $this->assertTrue($stringType->isType('test'));
        $this->assertFalse($stringType->isType(1));
    }

    public function testPrimitiveArray()
    {
        foreach (PrimitiveType::cases() as $primitive) {
            $primitiveType = new PrimitiveArrayType(name: 'primitiveType', type: $primitive);

            $trueType = match ($primitive) {
                PrimitiveType::STRING => ['test', 'test2'],
                PrimitiveType::INT => [1, 2],
                PrimitiveType::FLOAT => [1.52, 2.52],
                PrimitiveType::OBJECT => [(object)['test' => 'test'], (object)['test2' => 'test2']],
                PrimitiveType::BOOL => [true, false],
            };

            $falseType = match ($primitive) {
                PrimitiveType::STRING => ['test', 1],
                PrimitiveType::INT => [1, 'test'],
                PrimitiveType::FLOAT => [1.52, 'test'],
                PrimitiveType::OBJECT => [new stdClass(), ['test2' => 2]],
                PrimitiveType::BOOL => [true, 'test'],
            };

            $this->assertTrue($primitiveType->isType($trueType));
            $this->assertFalse($primitiveType->isType($falseType));
        }
    }

    public function testIntBackedEnumerationType(): void
    {
        $type = new EnumerationType('permission', RolePermission::class);

        $this->assertTrue($type->isType(RolePermission::READ));
        $this->assertFalse($type->isType(IssueCategory::INVALID));
        $this->assertFalse($type->isType(5));
    }

    public function testStringBackedEnumerationTypeConversion(): void
    {
        $type = new EnumerationType('issueCategory', IssueCategory::class);

        $this->assertSame(IssueCategory::GENERAL, $type->convert('general'));
        $this->assertSame(null, $type->convert('INVALID'));
    }

    public function testStringBackedEnumerationTypeCaseInsensitiveConversion(): void
    {
        $type = new EnumerationType('issueCategory', IssueCategory::class, false);

        $this->assertSame(IssueCategory::GENERAL, $type->convert('GENERAL'));
        $this->assertSame(IssueCategory::BUG, $type->convert('Bug'));
        $this->assertSame(null, $type->convert('test123'));
        $this->assertSame(IssueCategory::ENHANCEMENT, $type->convert('enhancement'));
    }

    public function testEnumerationTypeInvalidData(): void
    {
        $type = new EnumerationType('permission', RolePermission::class);

        $this->assertFalse($type->isType(new stdClass()));
    }

    public function testEnumerationArrayType(): void
    {
        $type = new EnumerationArrayType('permissions', RolePermission::class);

        $this->assertTrue($type->isType([RolePermission::READ, RolePermission::WRITE]));
        $this->assertFalse($type->isType([1, 2, 5]));
        $this->assertFalse($type->isType([IssueCategory::INVALID]));
        $this->assertFalse($type->isType(1));
    }

    public function testPureEnumerationTypeIsNotSupported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Non-backed enumerations cannot be mapped.');

        new EnumerationType('issueState', IssueState::class);
    }

    public function testAttributeRepetitionOnProperty()
    {
        $class = new ReflectionClass(AttributeDuplication::class);
        $property = $class->getProperty('property');
        $attributes = $property->getAttributes(PrimitiveArrayType::class);

        $this->assertCount(2, $attributes);

        $intAttribute = $attributes[0]->newInstance();
        $this->assertEquals('property', $intAttribute->name);
        $this->assertEquals(PrimitiveType::INT, $intAttribute->type);

        $floatAttribute = $attributes[1]->newInstance();
        $this->assertEquals('property', $floatAttribute->name);
        $this->assertEquals(PrimitiveType::FLOAT, $floatAttribute->type);
    }
}

<?php

namespace Tnapf\JsonMapper\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Tnapf\JsonMapper\Attributes\AnyArray;
use Tnapf\JsonMapper\Attributes\AnyType;
use Tnapf\JsonMapper\Attributes\ArrayCallbackType;
use Tnapf\JsonMapper\Attributes\BoolType;
use Tnapf\JsonMapper\Attributes\CallbackType;
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
use Tnapf\JsonMapper\Exception\InvalidValueTypeException;
use Tnapf\JsonMapper\Mapper;
use Tnapf\JsonMapper\Tests\Fakes\IssueCategory;
use Tnapf\JsonMapper\Tests\Fakes\IssueState;
use Tnapf\JsonMapper\Tests\Fakes\RolePermission;
use Tnapf\JsonMapper\Tests\Fakes\AttributeDuplication;

class AttributeTest extends TestCase
{
    public function testCallbackType(): void
    {
        $callbackType = new CallbackType(
            name: 'callbackType',
            callback: static fn (string $value) => $value . '5',
            isTypeCallback: static fn (mixed $value) => ($value === 'test')
        );

        $this->assertTrue($callbackType->isType('test'));
        $this->assertFalse($callbackType->isType('test2'));
        $this->assertSame('test5', $callbackType('test', new Mapper()));
    }

    public function testArrayCallbackType(): void
    {
        $arrayCallbackType = new ArrayCallbackType(
            name: 'arrayCallbackType',
            callback: static fn (string $value) => $value . '5',
            isTypeCallback: static fn (mixed $value) => ($value === 'test')
        );

        $this->assertTrue($arrayCallbackType->isType(['test']));
        $this->assertFalse($arrayCallbackType->isType(['test2']));
        $this->assertSame(['test5'], $arrayCallbackType(['test'], new Mapper()));
    }

    public function testAnyArray(): void
    {
        $anyArray = new AnyArray(name: 'anyArray');

        $this->assertTrue($anyArray->isType(['test', 1, 1.52]));
        $this->assertFalse($anyArray->isType('test'));
    }

    public function testAnyType(): void
    {
        $anyType = new AnyType(name: 'anyType');

        $this->assertTrue($anyType->isType('test'));
        $this->assertTrue($anyType->isType(1));
    }

    public function testBoolType(): void
    {
        $boolType = new BoolType(name: 'boolType');

        $this->assertTrue($boolType->isType(true));
        $this->assertTrue($boolType->isType(false));
        $this->assertFalse($boolType->isType('test'));
    }

    public function testFloatType(): void
    {
        $floatType = new FloatType(name: 'floatType');

        $this->assertTrue($floatType->isType(1.52));
        $this->assertFalse($floatType->isType('test'));
    }

    public function testIntType(): void
    {
        $intType = new IntType(name: 'intType');

        $this->assertTrue($intType->isType(1));
        $this->assertFalse($intType->isType('test'));
    }

    public function testObjectArray(): void
    {
        $objectArray = new ObjectArrayType(name: 'objectArray', class: stdClass::class);

        $this->assertTrue($objectArray->isType([new stdClass(), new stdClass()]));
        $this->assertFalse($objectArray->isType(['test']));
        $this->assertFalse($objectArray->isType('test'));
    }

    public function testObjectArrayThrowsIfClassDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NonExistentClass does not exist');

        new ObjectArrayType(name: 'objectArray', class: 'NonExistentClass');
    }

    public function testObjectType(): void
    {
        $objectType = new ObjectType(name: 'objectType', class: stdClass::class);

        $this->assertTrue($objectType->isType(new stdClass()));
        $this->assertFalse($objectType->isType('test'));
    }

    public function testObjectTypeThrowsIfClassDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NonExistentClass does not exist');

        new ObjectType(name: 'objectType', class: 'NonExistentClass');
    }

    public function testStringType(): void
    {
        $stringType = new StringType(name: 'stringType');

        $this->assertTrue($stringType->isType('test'));
        $this->assertFalse($stringType->isType(1));
    }

    public function testPrimitiveArray(): void
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
        $this->assertNull($type->convert('INVALID'));
    }

    public function testStringBackedEnumerationTypeCaseInsensitiveConversion(): void
    {
        $type = new EnumerationType('issueCategory', IssueCategory::class, false);

        $this->assertSame(IssueCategory::GENERAL, $type->convert('GENERAL'));
        $this->assertSame(IssueCategory::BUG, $type->convert('Bug'));
        $this->assertSame(IssueCategory::ENHANCEMENT, $type->convert('enhancement'));
        $this->assertNull($type->convert('test123'));
    }

    public function testEnumerationTypeThrowsIfDataTypeIsInvalid(): void
    {
        $this->expectException(InvalidValueTypeException::class);
        $this->expectExceptionMessage('Expected value of type int or string, got object');

        $type = new EnumerationType('issueCategory', IssueCategory::class);
        $type->convert(new stdClass());
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

    public function testEnumerationArrayTypeConversion(): void
    {
        $type = new EnumerationArrayType('permissions', RolePermission::class);

        $this->assertSame([RolePermission::READ, RolePermission::WRITE], $type->convert([1, 2]));
        $this->assertSame([null, RolePermission::UNRESTRICTED], $type->convert([5, 4]));
    }

    public function testEnumerationArrayTypeConversionCaseSensitive(): void
    {
        $type = new EnumerationArrayType('issueCategory', IssueCategory::class);

        $this->assertSame([IssueCategory::GENERAL, IssueCategory::BUG], $type->convert(['general', 'bug']));
        $this->assertSame([IssueCategory::ENHANCEMENT, null], $type->convert(['enhancement', 'INVALID']));
        $this->assertSame([null], $type->convert(['Invalid']));
    }

    public function testEnumerationArrayTypeConversionCaseInsensitive(): void
    {
        $type = new EnumerationArrayType('issueCategory', IssueCategory::class, false);

        $this->assertSame([IssueCategory::GENERAL, IssueCategory::ENHANCEMENT], $type->convert(['GENERAL', 'enhancement']));
        $this->assertSame([IssueCategory::ENHANCEMENT, null], $type->convert(['enhancement', 'Invalid?']));
    }

    public function testEnumerationArrayTypeThrowsOnInvalidValueType(): void
    {
        $this->expectException(InvalidValueTypeException::class);
        $this->expectExceptionMessage('Expected value of type array, got string');

        $type = new EnumerationArrayType('issueCategory', IssueCategory::class);
        $type->convert('general');
    }

    public function testPureEnumerationTypeIsNotSupported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Non-backed enumerations cannot be mapped.');

        new EnumerationType('issueState', IssueState::class);
    }

    public function testPureEnumerationArrayTypeIsNotSupported(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Non-backed enumerations cannot be mapped.');

        new EnumerationArrayType('issueState', IssueState::class);
    }

    public function testEnumerationTypeThrowsIfEnumDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NonExistentEnum does not exist.');

        new EnumerationType(name: 'enumerationType', enum: 'NonExistentEnum');
    }

    public function testEnumerationArrayThrowsIfEnumDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NonExistentEnum does not exist.');

        new EnumerationArrayType(name: 'enumerationArray', enum: 'NonExistentEnum');
    }

    public function testAttributeRepetitionOnProperty(): void
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

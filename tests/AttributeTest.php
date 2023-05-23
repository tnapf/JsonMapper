<?php

namespace Tnapf\JsonMapper\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Tnapf\JsonMapper\Attributes\AnyArray;
use Tnapf\JsonMapper\Attributes\AnyType;
use Tnapf\JsonMapper\Attributes\BoolType;
use Tnapf\JsonMapper\Attributes\ObjectArrayType;
use Tnapf\JsonMapper\Attributes\ObjectType;
use Tnapf\JsonMapper\Attributes\FloatType;
use Tnapf\JsonMapper\Attributes\PrimitiveType;
use Tnapf\JsonMapper\Attributes\IntType;
use Tnapf\JsonMapper\Attributes\PrimitiveArrayType;
use Tnapf\JsonMapper\Attributes\StringType;

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
                PrimitiveType::OBJECT => [(object) ['test' => 'test'], (object) ['test2' => 'test2']],
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
}

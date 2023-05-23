<?php

namespace Tnapf\JsonMapper\Tests;

use PHPUnit\Framework\TestCase;
use Tnapf\JsonMapper\Attributes\SnakeToCamelCase;

class CaseConversionTest extends TestCase
{
    public function testSnakeToCamelCase()
    {
        $converter = new SnakeToCamelCase();
        $snakeCase = 'snake_case';
        $camelCase = 'snakeCase';

        $this->assertSame($snakeCase, $converter->convertFromCase($snakeCase), 'Snake case should not be converted');
        $this->assertSame($camelCase, $converter->convertToCase($camelCase), 'Camel case should not be converted');
        $this->assertSame($snakeCase, $converter->convertFromCase($snakeCase), 'Snake case should not be changed');
        $this->assertSame($camelCase, $converter->convertToCase($camelCase), 'Camel case should not be changed');
    }
}

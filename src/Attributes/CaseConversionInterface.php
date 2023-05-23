<?php

namespace Tnapf\JsonMapper\Attributes;

interface CaseConversionInterface
{
    public function convertToCase(string $string): string;

    public function convertFromCase(string $string): string;
}

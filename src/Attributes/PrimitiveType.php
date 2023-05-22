<?php

namespace Tnapf\Jsonmapper\Attributes;

enum Primitive: string
{
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case OBJECT = 'object';
    case BOOL = 'bool';
}

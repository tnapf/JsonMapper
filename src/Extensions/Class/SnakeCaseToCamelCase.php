<?php

namespace Tnapf\JsonMapper\Extensions\Class;

use Attribute;
use Tnapf\JsonMapper\Extension\ClassExtension;

#[Attribute()]
class SnakeCaseToCamelCase extends ClassExtension
{
    public function parseData(object &$instance, array &$data): void
    {
        foreach ($data as $key => $value) {
            $data[$this->convertCase($key)] = $value;
            unset($data[$key]);
        }
    }

    private function convertCase(string $snake): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $snake))));
    }
}

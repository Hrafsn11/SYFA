<?php 

namespace App\Enums;

use ReflectionClass;

trait EnumTrait
{
    public static function getConstants() {
        $reflectionClass = new ReflectionClass(static::class);
        return array_values($reflectionClass->getConstants());
    }
}
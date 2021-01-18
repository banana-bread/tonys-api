<?php

namespace Tests;

use ReflectionMethod;

class TestUtils 
{
    public static function callMethod($object, string $method, array $parameters = [])
    {
        $className = get_class($object);
        $reflection = new ReflectionMethod($className, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $parameters);
    }
}

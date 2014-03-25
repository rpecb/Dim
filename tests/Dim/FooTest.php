<?php

class FooTest extends PHPUnit_Framework_TestCase
{
    public function testGetReflectionParametersWithScope()
    {
        $dim = new Dim;
        $dim->scope('foo')->set(new stdClass);
        $dim->scope('foo')->set(new Service('Foo1'));
        $dim->scope('foo')->get('Foo1');
    }
} 
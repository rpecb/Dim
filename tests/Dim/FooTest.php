<?php

class FooTest extends PHPUnit_Framework_TestCase
{
    public function testGetReflectionParametersWithScope()
    {
        $dim = new Dim;
        $dim->scope('foo')->scope('bar')->scope('foobar')->set(new stdClass);
        $dim->scope('foo')->scope('bar')->scope('foobar')->set(new Service('Foo1'));
        $dim->scope('foo')->scope('bar')->scope('foobar')->get('Foo1');
    }
} 
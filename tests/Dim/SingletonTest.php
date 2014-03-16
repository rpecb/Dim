<?php

class SingletonTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $singleton1 = new Singleton($this->getMock('Dim'), 'stdClass');
        $foo1 = $singleton1->get();
        $this->assertInstanceOf('stdClass', $foo1);
        $this->assertSame($foo1, $singleton1->get());

        $singleton2 = new Singleton($this->getMock('Dim'), 'stdClass');
        $foo2 = $singleton2->get();
        $this->assertInstanceOf('stdClass', $foo2);
        $this->assertSame($foo2, $singleton2->get());

        $this->assertNotSame($foo1, $foo2);
    }
}
 
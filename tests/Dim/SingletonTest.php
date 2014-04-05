<?php

class SingletonTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $args1 = array(1, 2, 3);
        $args2 = array(3 => 4, 5, 6);
        $dim = $this->getMock('Dim');
        $service = $this->getMockBuilder('Singleton')->setMethods(array('resolveClass'))->setConstructorArgs(
            array('stdClass', $args2)
        )->getMock();
        $service->staticExpects($this->once())->method('resolveClass')->with(
            $this->stringContains('stdClass'),
            $this->identicalTo($args1 + $args2),
            $this->identicalTo($dim)
        )->will($this->returnValue(new stdClass));
        $foo = $service->get($args1, $dim);
        $bar = $service->get($args1, $dim);
        $this->assertInstanceOf('stdClass', $foo);
        $this->assertInstanceOf('stdClass', $bar);
        $this->assertSame($foo, $bar);
    }
}
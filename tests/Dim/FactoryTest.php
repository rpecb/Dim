<?php

class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testConstructException()
    {
        new Factory($this->getMock('Dim'), 'Foo');
    }

    public function testGet()
    {
        $factory = new Factory($this->getMock('Dim'), function () {
            return new Foo;
        });
        $this->assertInstanceOf('Foo', $factory->get());
    }
}
 
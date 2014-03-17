<?php

class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A callable expected.
     */
    public function testConstructException()
    {
        new Factory($this->getMock('Dim'), 'stdClass');
    }

    public function testGet()
    {
        $factory = new Factory($this->getMock('Dim'), function () {
            return new stdClass;
        });
        $this->assertInstanceOf('stdClass', $factory->get());
    }
}
 